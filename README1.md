# phalcon-resque
Setup background jobs with Phalcon and Resque

Installation
Install Phalcon

Please refer the instruction here for installation of Phalcon.

Install Redis

Please follow the instructions here for installation of Redis server.

Install project

You can clone or download the project from phlacon-resque and put this under your web root folder.

Installation of php-resque

Once you have downloaded the project simply run the composer using following command


1
$ php composer.phar install
Setup
Start Redis server


1
$ redis-2.8.8/src/redis-server
Setup Queue of Resque


1
2
3
$ cd vendor/chrisboulton/php-resque
 
$ INTERVAL=10 QUEUE=default APP_INCLUDE=../../../public/bg.php php resque.php
Ok so now we have redis up and running and worker is started lets create a background job, open the browser and hit the home page, http://localhost/phalcon-resque/public/
Clicking on “Set Background Job!” will put a job in the queue, for demo purpose the background job just does a small work of storing some data in a text file. We are passing random values from frontend which will be stored in  a text file.

If everything went well you will see a text “Value: <someid>” within logs/res.log file.

Here is quick overview of some of the important codes,

index.php


1
2
3
4
5
6
7
8
9
<?php
….
….
….
include('../vendor/autoload.php');
$application = new \Phalcon\Mvc\Application();
$application->setDI($di);
 
?>
make sure you include the autoload file as show above, this will include the php-resque files within the project.

IndexController.php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
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
Important function here is the Resque::enque which takes 4 parameters

1st   :  (string) name of Queue, this should be same as the one which was used while starting the worker.
2nd :  (string) ClassName of the background job, this class should have a method called perform.
3rd  :  (array) argument array which can be passed to background class
4th  :  (boolean) if true will return job id which can be used for tracking purpose.

Once the job is created it will return the job id which can be used for tracking the status of job.

I have created a function “getJobStatus()” which will use the Resque_Job_Status class and return the status of the job.

TestBackgroundJob.php


1
2
3
4
5
6
7
8
9
10
11
<?php
class TestBackGroundJob
{
function perform()
{
$id = $this->args['id'];
$data = "\n value: " . $id;
$file = __DIR__."/../../log/res.log";
file_put_contents($file,$data,FILE_APPEND);
}
}
above function is self explanatory only important thing to notice here is how to access the arguments passed.

bg.php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
<?php
 
try {
 
//Register an autoloader
$loader = new \Phalcon\Loader();
$loader->registerDirs(
array(
__DIR__.'/../app/background-jobs/'
), true
)->register();
 
//Create a DI
$di = new Phalcon\DI\FactoryDefault();
}
catch(\Phalcon\Exception $e) {
echo "PhalconException: ", $e->getMessage();
}
This will make sure the Backgrond jobs are accessible by workers without need to run application

*Note: Whenever you make any changes within your code make sure you restart the php-resque worker, otherwise the new code changes would not be reflected.

I hope this will be useful for some one to get started with setting up php-resque with Phalcon. In case you face any problem or have any questions please use the comment section below.
