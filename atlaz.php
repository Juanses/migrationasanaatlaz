<?php
require_once __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);

define('COMPANYID', '');
define('ATLAZTOKEN', '');
define('API_URL',"https://api.atlaz.io/public-api/");

function send_get_request($api,$vars){
  $client = new \GuzzleHttp\Client();
  //var_dump(create_request($api,$vars));
  $response = $client->request('GET',create_request($api,$vars),['headers' => ['authorization'=>"Bearer ".ATLAZTOKEN]]);
  $obj = json_decode($response->getBody(),true);
  return $obj["data"];
}

function send_patch_request($api,$vars,$data){
  //var_dump(create_request($api,$vars));
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => create_request($api,$vars),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "PATCH",
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer ".ATLAZTOKEN,
      "cache-control: no-cache",
      "content-type: application/json-patch+json",
    ),
  ));
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

}

function send_post_request($api,$vars,$data){
  $client = new \GuzzleHttp\Client();
  //var_dump(create_request($api,$vars));
  $response = $client->post(create_request($api,$vars), ['headers' => ['authorization'=>"Bearer ".ATLAZTOKEN],'json' => $data]);
  $obj = json_decode($response->getBody(),true);
  return $obj["data"];
}

function create_request($api,$variablearray){
 return API_URL.$api."?".http_build_query($variablearray);
}

function get_boards($projectid){
  $vars = array (
    "board"=>25627,
    "project"=>$projectid,
    "company_id"=>COMPANYID
  );
  return send_get_request("boards",$vars);
}

function getboardidbyname($boards,$name){
  for ($i=0; $i < sizeof($boards) ; $i++) {
    if ($boards[$i]["attributes"]["name"] == $name){
    return $boards[$i]["id"];
    }
  }
  return false;
}

function getcolumns($board){
  $vars = array (
    "board"=>25627,
    "expand"=>"tasks",
    "limit"=>100,
    "offset"=>0,
    "company_id"=>COMPANYID
  );
  return send_get_request("columns",$vars);
}

function createtask($title,$column,$swimlane){
  $vars = array (
    "expand"=>"users",
    "company_id"=>COMPANYID
  );
  $param = array(
  "title"=> $title,
  "column"=>$column,
  "swimlane"=>$swimlane
  );
  $data = send_post_request("tasks",$vars,$param);
  return $data[0]["id"];
}

function add_label($taskid,$labelcode){
  $vars = array(
  "expand"=>"users",
  "company_id"=>COMPANYID,
  );
  $data = array(
    "op"=>"add",
    "path"=>"/labels/-",
    "value"=>$labelcode
  );
  $data = "[".json_encode($data, JSON_FORCE_OBJECT)."]";
  send_patch_request("tasks/".$taskid,$vars,$data);
}

function change_swimlane($taskid,$swimlane){
  $vars = array(
  "expand"=>"users",
  "company_id"=>COMPANYID,
  );
  $data = array(
    "op"=>"replace",
    "path"=>"/swimlane",
    "value"=>$swimlane
  );
  $data = "[".json_encode($data, JSON_FORCE_OBJECT)."]";
  send_patch_request("tasks/".$taskid,$vars,$data);
}

function get_swimlanes($board_id){
  $vars = array (
    "board"=>$board_id,
    "sort"=>1,
    "limit"=>100,
    "company_id"=>COMPANYID,
    "offset"=>0,
  );
  return send_get_request("swimlanes",$vars);
}

function list_tasks($boardid){
  $vars = array (
    "expand"=>"worklogs",
    "board"=>$boardid,
    "limit"=>10000,
    "offset"=>0,
    "columns_id"=>191163,
    "company_id"=>COMPANYID,
    "archived"=>0
  );
  return send_get_request("tasks",$vars);
}
