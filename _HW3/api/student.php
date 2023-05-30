<?php

//error_reporting(E_ALL);
require("helpers/server_response.php");


$request = new ClientRequest();
$dataSource = new DataSource("data.json");   // This is where the users' iniput goes 
$response = new ServerResponse($request, $dataSource);

$response->process();

function POST(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);
    $newName = $request->post['name'] ?? false;

    $cwidTop = 0;
    foreach($data as $student){
        $num = (int)$student['cwid'];
        if($num > $cwidTop){
            $cwidTop = $num;
        }
    }

    $newCwid = $cwidTop + 1;   

    $newPerson = array(
        "name" => $newName,
        "cwid" => $newCwid,
        "grades" => array(
            "hw1"=>"",
            "hw2"=>"",
            "hw3"=>"",
            "hw4"=>""
        )
    );

    array_push($data, $newPerson);

    $newJson = json_encode($data);

    file_put_contents($dataSource->writePath, $newJson);

    $response->status = "OK";
    $response->outputJSON($newPerson);    // exit(json_encode($this->build())); //PHP json_encode  //JSON 
}
function GET(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);
    $output = [];
    $id = $request->get['id'] ?? false;

    if ($id != false) {
        $cwid = $id;
        foreach ($data as $student) {
            if (array_key_exists("cwid", $student) && $student["cwid"] == $cwid) {
                array_push($output, $student);
                break;
            }
        }
    } else {
        $output = $data;
    }

    $response->status = "OK";
    $response->outputJSON($output);
}

function PUT(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    //change one record by matvhing "cwid"
    // dataSource from the database 
    $data = $dataSource->JSON(true);

    //get the "cwid" from user input
    $cwid = $request->put['cwid'] ?? false;

    $newData = array(
        //get "name" from user input
        "name"=>$request->put['name'],
        "cwid"=>$cwid,
        "grades"=>array()
    );

    $target = 0;

    //forEach parsing JSON string 
    // data is the whole JSON string, look for the whole matching object in the JSON string 
    foreach($data as $key =>$student){
        if($student['cwid'] == $cwid){
            // if cwid matched, the newData' grade attributes equal to this JSON string's grade attributes 
            $newData["grades"] = $student["grades"];

            // and recording the key to target 
            $target = $key;
        }
    }

    // Adding the whole new array to $data JSON // only one row of data, update the name of this row of data
    $data[$key] = $newData;

    //JSON with one row new data , php object to JSON string  // convert it to JSON before writting it to DB ?
    $newJson = json_encode($data);


    file_put_contents($dataSource->writePath, $newJson);
    $response->status = "OK";
    $response->outputJSON($newData);
}

// function PUT(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
// {
//     $data = $dataSource->JSON(true);

//     $cwid = $request->put['cwid'] ?? false;

//     $newData = array(
//         "name"=>$request->put['name'],
//         "cwid"=>$cwid,
//         "grades"=>array()
//     );

//     $target = 0;

//     foreach($data as $key =>$student){
//         if($student['cwid'] == $cwid){
//             $newData["grades"] = $student["grades"];
//             $target = $key;
//             $data[$key]["name"] = $request->put['name']; // update the name of the old data
//         }
//     }

//     $newJson = json_encode($data);

//     file_put_contents($dataSource->writePath, $newJson);

//     $response->status = "OK";
//     $response->outputJSON($newData);
// }


function DELETE(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);
    $data = array("Method" => "Delete");

    $response->status = "OK";
    $response->outputJSON($data);
}


exit;

$dataPath = __DIR__ . "\data.json";

$json = file_get_contents($dataPath);

$data = json_decode($json, true);

echo ("<pre>" . print_r($data, true) . "</pre>");

$newJson = json_encode($data);

//file_put_contents($dataPath, $newJson);

Header("Content-Type: application/json; charset=utf-8");
exit(json_encode($testResults));
