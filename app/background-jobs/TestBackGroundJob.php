<?php
class TestBackGroundJob
{
    function perform()
    {
        $id = $this->args['id'];
        $data = "\n value: " . $id;
        echo $data;
        $file = __DIR__."/../../log/res.log";
        file_put_contents($file,$data,FILE_APPEND);
    }
}