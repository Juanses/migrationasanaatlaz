<?php
require_once __DIR__ . '/vendor/autoload.php';
define('ASANATOKEN', '');

function get_field_value($data,$fieldname){
  if (isset($data["custom_fields"])) {
    foreach ($data["custom_fields"] as $field) {
      if (isset($field["name"]) && $field["name"] == $fieldname && $field["enum_value"]["name"] != NULL)
      {
        return $field["enum_value"]["name"];
        break;
      }
    }
  }
  return "";
}

function get_tags_from_task($taskid){
    $client = new \GuzzleHttp\Client();
  $response = $client->request('GET', 'https://app.asana.com/api/1.0/tasks/'.$taskid.'/tags', ['headers' => ['authorization'=>"Bearer ".ASANATOKEN]]);
  $obj = json_decode($response->getBody(),true);
  return $obj["data"];
}


function get_task_info($task){
  $client = new \GuzzleHttp\Client();
  $response = $client->request('GET', 'https://app.asana.com/api/1.0/tasks/'.$task, ['headers' => ['authorization'=>"Bearer ".ASANATOKEN]]);
  $obj = json_decode($response->getBody(),true);
  return $obj["data"];
}

function get_projectid_by_name($workspace, $name){
  $projects = get_projects($workspace);
  $id = 0;
  foreach ($projects as $project) {
    if ($project["name"] == $name) {
        $id = $project["id"];
        break;
    }
  }
  return $id;
}

function get_tasks($project){
  $client = new \GuzzleHttp\Client();
  $response = $client->request('GET', 'https://app.asana.com/api/1.0/projects/'.$project.'/tasks/', ['headers' => ['authorization'=>"Bearer ".ASANATOKEN]]);
  $obj = json_decode($response->getBody(),true);
  return $obj["data"];
}

function get_workspaces(){
  $client = new \GuzzleHttp\Client();
  $response = $client->request('GET', 'https://app.asana.com/api/1.0/workspaces/', ['headers' => ['authorization'=>"Bearer ".ASANATOKEN]]);
  $obj = json_decode($response->getBody(),true);
  return $obj["data"];
}

function get_projects($workspace){
  //https://app.asana.com/api/1.0/workspaces/367532856051497/projects
  $client = new \GuzzleHttp\Client();
  $response = $client->request('GET','https://app.asana.com/api/1.0/workspaces/'.$workspace.'/projects', ['headers' => ['authorization'=>"Bearer ".ASANATOKEN]]);
  $obj = json_decode($response->getBody(),true);
  return $obj["data"];
}

function get_late($client,$workspaceid,$assignee = "me"){
  $response = $client->request('GET', 'https://app.asana.com/api/1.0/tasks/', ['headers' => ['authorization'=>"Bearer "],"query"=>["workspace"=>$workspaceid,"assignee"=>$assignee,"completed_since"=>"now"]]);
  $obj = json_decode($response->getBody(),true);
  $ahora = new DateTime("now");
  $respuesta = array(); //response array
  foreach ($obj["data"] as $value){
    $response = $client->request('GET', 'https://app.asana.com/api/1.0/tasks/'.$value["id"], ['headers' => ['authorization'=>"Bearer "]]);
    $matrix = json_decode($response->getBody(),true);
    if (isset($matrix["data"]["due_on"])){
      $fecha = DateTime::createFromFormat('Y-m-d',$matrix["data"]["due_on"]);
      if($fecha < $ahora){
        array_push($respuesta, $matrix["data"]);
      };
    }
  }
  return $respuesta;
}

?>
