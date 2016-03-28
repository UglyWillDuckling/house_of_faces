<?php
    use Tricky\helpers\Request\Request;

    $strap->setPost('removeTmpPicture', 'removeTmpPicture', function() use($strap){

        $source = Request::getPost('source');

        if($source)
        {
            $msg = [];
            $pos = strlen($strap->baseUrl);

            $relPath = substr($source, $pos);           
            $path = INC_FOLDER . $relPath;
            

            $msg['ok'] = ( unlink($path) );

            echo json_encode($msg);
        }
    }, ['member']);