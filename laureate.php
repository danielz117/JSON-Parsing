<?php
// get the id parameter from the request
$id = intval($_GET['id']);

// set the Content-Type header to JSON, 
// so that the client knows that we are returning JSON data
header('Content-Type: application/json');

/*
   Send the following fake JSON as the result
   {  "id": $id,
      "givenName": { "en": "A. Michael" },
      "familyName": { "en": "Spencer" },
      "affiliations": [ "UCLA", "White House" ]
   }
 */

$db = new mysqli('localhost', 'cs143', '', 'class_db');

if ($db->connect_errno > 0) {
	die('Unable to connect to database [' . $db->connect_error .']');
}

$queryLaureate = "SELECT L.givenName, L.familyName, L.gender, L.m_date, L.city, L.country
		FROM laureates L
		WHERE L.id = $id";

$rs1 = $db->query($queryLaureate);

while($row = $rs1->fetch_assoc()) {
	$givenName = $row['givenName'];
	$familyName = $row['familyName'];
	$gender = $row['gender'];
	$laureateDate = $row['m_date'];
	$laureateCity = $row['city'];
	$laureateCountry = $row['country'];
	if ($laureateCity == "" && $laureateCountry == "") {
		$locationObj = (object) [];
	}
	else {
	$locationObj = (object) [
            "city" => (object) [
                "en" => $laureateCity
            ],
            "country" => (object) [
                "en" => $laureateCountry
            ],
	];
	}
}

$queryPrizes = "SELECT awardYear, category, sortOrder
		FROM nobelPrizes, awarded
		WHERE awarded.id = $id AND nobelPrizes.nobelKey = awarded.nobelKey";

$rs2 = $db->query($queryPrizes);

while($row = $rs2->fetch_assoc()) {
        $awardYear = $row['awardYear'];
        $category = $row['category'];
        $sortOrder = $row['sortOrder'];
}

$queryAff = "SELECT name, city, country
		FROM affiliations, awarded
		WHERE awarded.id = $id AND affiliations.affKey = awarded.affKey";
$rs3 = $db->query($queryAff);
$affArray = array();
while ($row = $rs3->fetch_assoc()) {
	$affName = $row['name'];
	$affCity = $row['city'];
	$affCountry = $row['country'];
	$insert = (object) [
		"name" => (object) [
			"en" => $affName
		],
		"city" => (object) [
			"en" => $affCity
		],
		"country" => (object) [
			"en" => $affCountry
		],
	];
	$affArray[] = $insert;
}

if ($gender != "org") {
$output = (object) [
    "id" => strval($id),
    "givenName" => (object) [
        "en" => $givenName
    ],
    "familyName" => (object) [
        "en" => $familyName
    ],
    "gender" => $gender,
    "birth" => (object) [
	"date" => $laureateDate,
	"place" => (object) [
	    "city" => (object) [
	    	"en" => $laureateCity
	    ],
	    "country" => (object) [
	    	"en" => $laureateCountry
	    ],
	],
    ], 

    "nobelPrizes" => array(
	(object) [
		"awardYear" => $awardYear,
		"category" => (object) [
			"en" => $category
		],
		"sortOrder" => $sortOrder,
		"affiliations" => $affArray,
	],
)
];
}
else {
$output = (object) [
    "id" => strval($id),
    "orgName" => (object) [
        "en" => $givenName
    ],
    "founded" => (object) [
        "date" => $laureateDate,
        "place" => $locationObj,
    ],
    "nobelPrizes" => array(
        (object) [
                "awardYear" => $awardYear,
                "category" => (object) [
                        "en" => $category
                ],
                "sortOrder" => $sortOrder,
                "affiliations" => $affArray,
	],
)
];
}
echo json_encode($output);

?>
