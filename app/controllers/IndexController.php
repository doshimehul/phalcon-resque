<?php

class IndexController extends Phalcon\Mvc\Controller 
{
	public function indexAction()
	{
        if(isset($_GET['setJob']))
        {
            //echo " set job";
            $arguments = array('id' => rand(0,10));
            $job_id = Resque::enqueue("default", "TestBackGroundJob", $arguments, true);

            echo Phalcon\Tag::linkTo("?status=true&job_id=" .$job_id, "Check Background Job Status");
            exit;
        }

        if(isset($_GET['status']))
        {
            echo " Job Status:" . $this->getJobStatus($_GET['job_id']);
            echo "<br />";
            echo Phalcon\Tag::linkTo("", "Home");
            exit;
        }


    }

    public function getJobStatus($jobId)
    {
        $status = new Resque_Job_Status($jobId);
        switch($status->get())
        {
            case Resque_Job_Status::STATUS_WAITING:
                return "waiting";
                break;
            case Resque_Job_Status::STATUS_RUNNING:
                return "running";
                break;
            case Resque_Job_Status::STATUS_FAILED:
                return "failed";
                break;
            case Resque_Job_Status::STATUS_COMPLETE:
                return "completed";
                break;
        }
    }

}
