<?php
if(class_exists('Phar')){
    $phar = new Phar('photo-classify.phar',0,'photo-classify.phar');
    $phar->buildFromDirectory(__DIR__);
    $phar->setStub($phar->createDefaultStub('phar.php','phar.php'));
    $phar->compressFiles(Phar::GZ);
}else{
    exit('No Phar module found !');
}