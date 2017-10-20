<?php
//phpinfo();
require_once __DIR__ . '/vendor/autoload.php';
ini_set('display_errors', 1);

include("asana.php");
include("atlaz.php");

$projectid = ; //
$boards = get_boards($projectid);
$board_id = getboardidbyname($boards,"Backlog");
$tasks = list_tasks($board_id);

//var_dump($tasks);
//var_dump(get_swimlanes($board_id));

for ($i=0; $i < sizeof($tasks); $i++) {
    if (preg_match("/demandeur/i", $tasks[$i]["attributes"]["title"])){
       change_swimlane($tasks[$i]["id"],57849);
    }
}

$firstcolumnid = getcolumns($board_id)[0]["id"];
$firstswimlaneid = get_swimlanes($board_id)[0]["id"];


$workspaces = get_workspaces();
$workspace = $workspaces[0]["id"];
$project_id = get_projectid_by_name($workspace, "WORKSPACENAME");
$tasks = get_tasks($project_id);
for ($i=0; $i < sizeof($tasks); $i++) {
    $asanaid = $tasks[$i]["id"];
    $asanatitle = $tasks[$i]["name"];
    $taskid = createtask($asanatitle,$firstcolumnid,$firstswimlaneid);
    $tags = get_tags_from_task($asanaid);
    for ($j=0; $j <sizeof($tags) ; $j++) {
        add_label($taskid,tag_id($tags[$j]["name"]));
    }
}

function tag_id($name){
    switch($name){
        case "Compte":
        $tag = ;
        break;
    }
    return $tag;
}
