<?php

class TaskProgressController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
            

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);

//		if(isset($_POST['TaskProgress']))
//		{
//			$model->attributes=$_POST['TaskProgress'];
//			if($model->save())
//				$this->redirect(array('view','id'=>$model->tp_id));
//		}
//
//		$this->render('create',array(
//			'model'=>$model,
//		));
            $model = false;
            if($_POST['task_id'])
            {   
                $task_id = $_POST['task_id'];
                $task_assignee = TaskAssignees::model()->findAll('task_id='.  intval($task_id));

                foreach ($task_assignee as $task_assignee_item) {
                    $account_id = $task_assignee_item->account_id;
                    
                    // Kiem tra xem da tao procress mat dinh cho account nay chua?
                    $assign_procress = TaskProgress::model()->getTaskProgress($task_id, $account_id);
                    count($assign_procress);
                    //END
                    
                    // Neu chua co progess mat dinh thi them progress ban dau cho account nay.
                    if(count($assign_procress)<=0)        
                        $model=  TaskProgress::model()->AddTaskProgress($task_id, $account_id);
                }

            }
            
            if($model)
                echo '{"status":"success"}';
            else
                echo '{"status":"fail"}';

	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
            if(isset($_POST['task_id']) && isset($_POST['account_id']))
            {
                $days_compl = $_POST['days_compl'];
                $percent_completed = $_POST['percent_completed'];
                $account_id = $_POST['account_id'];
                for($i=0;$i<count($days_compl);$i++)
                {
                    $task_id=$_POST['task_id'];
                    $model =  TaskProgress::model()->AddTaskProgress($task_id, $account_id[$i], $percent_completed[$i], $days_compl[$i]);
                }
            }
            
            if($model)
                echo '{"status":"success"}';
            else
                echo '{"status":"fail"}';
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('TaskProgress');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new TaskProgress('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TaskProgress']))
			$model->attributes=$_GET['TaskProgress'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return TaskProgress the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=TaskProgress::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param TaskProgress $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='task-progress-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
