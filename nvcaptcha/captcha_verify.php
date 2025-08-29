<?php
session_start();
$data=json_decode(file_get_contents("php://input"),true);

if(!$data || empty($data['token']) || !isset($_SESSION['slider_captcha'])){
    echo json_encode(["ok"=>false,"msg"=>"Invalid request"]); exit;
}

$cap=&$_SESSION['slider_captcha'];

// Token & expiry check
if($data['token']!==$cap['token']){ echo json_encode(["ok"=>false,"msg"=>"Bad token"]); exit; }
if(time()-$cap['created']>120){ echo json_encode(["ok"=>false,"msg"=>"Expired, reload"]); exit; }

// Display gap request
if(isset($data['action']) && $data['action']==="display"){
    echo json_encode(["ok"=>true,"pos"=>$cap['target']]); exit;
}

// Verify
$pos=(int)$data['pos'];
$match=100-abs($cap['target']-$pos); // how close they were
if(abs($cap['target']-$pos)<=5){ // within 5% is success
    echo json_encode(["ok"=>true,"match"=>$match]);
    unset($_SESSION['slider_captcha']); // regenerate next time
} else {
    $cap['fails']++;
    if($cap['fails']>5){ echo json_encode(["ok"=>false,"msg"=>"Too many attempts"]); exit; }
    echo json_encode(["ok"=>false,"msg"=>"Not correct, try again"]);
}
