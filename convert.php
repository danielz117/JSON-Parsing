<?php 

$laureatesFile = fopen("Laureates.del", "w");
$nobelFile = fopen("NobelPrizes.del", "w");
$affFile = fopen("Affiliations.del", "w");
$awardedFile = fopen("Awarded.del", "w");

// read JSON data
$file_content = file_get_contents("/home/cs143/data/nobel-laureates.json");
$data = json_decode($file_content, true);
$laureatesDict = array();
$nobelDict = array();
$affDict = array();


// get the id, givenName, and familyName of the first laureate
foreach ($data["laureates"] as $laureate) {
$id = $laureate["id"];
if (in_array($id, $laureatesDict)) {
	continue;
}
$laureatesDict[] = $id;
$givenName = $laureate["givenName"]["en"] ?? $laureate["orgName"]["en"] ?? null;
$familyName = $laureate["familyName"]["en"] ?? 'org';
$gender = $laureate["gender"] ?? 'org';
$date = $laureate["birth"]["date"] ?? $laureate["founded"]["date"] ?? null;
$city = $laureate["birth"]["place"]["city"]["en"] ?? $laureate["founded"]["place"]["city"]["en"] ?? null;
$country = $laureate["birth"]["place"]["country"]["en"] ?? $laureate["founded"]["place"]["country"]["en"] ?? null;
$laureatesTxt = $id . ',' . $givenName . ',' . $familyName . ',' . $gender . ',' . $date . ',' . $city . ',' . $country . PHP_EOL;
fwrite($laureatesFile, $laureatesTxt);

foreach($laureate["nobelPrizes"] as $nobel) {
$awardYear = $nobel["awardYear"] ?? null;
$category = $nobel["category"]["en"] ?? null;
$sortOrder = $nobel["sortOrder"] ?? null;
$nobelKey = $awardYear . '|' . $category . '|' . $sortOrder;
if (in_array($nobelKey, $nobelDict)) {
	continue;
}
$nobelDict[] = $nobelKey;
$nobelTxt = $nobelKey . ',' . $awardYear . ',' . $category . ',' . $sortOrder . PHP_EOL;
fwrite($nobelFile, $nobelTxt);
//echo $nobelKey . "\t" . PHP_EOL;
$affKey = $nobel["affiliations"] ?? null;
if ($affKey != null) {
foreach($nobel["affiliations"] as $aff) {
$affName = $aff["name"]["en"] ?? null;
$affCity = $aff["city"]["en"] ?? null;
$affCountry = $aff["country"]["en"] ?? null;
$affKey = $affName . '|' . $affCity . '|' . $affCountry;
$affTxt = $affKey . ',' . $affName . ',' . $affCity . ',' . $affCountry . PHP_EOL;
$awardedTxt = $id . ',' . $nobelKey . ',' . $affKey . PHP_EOL;
fwrite($awardedFile, $awardedTxt);
if (in_array($affKey, $affDict)) {
	continue;
}
$affDict[] = $affKey;
fwrite($affFile, $affTxt);
if ($id == '265'){ echo $awardedTxt;
echo 'something';
}
}
}
else {
	$awardedTxt = $id . ',' . $nobelKey . ',' . $affKey . PHP_EOL;
	fwrite($awardedFile, $awardedTxt);
	if ($id == '265') {echo $awardedTxt;
	echo 'something';
	}
}
//echo $id . "\t" . PHP_EOL;
}
}
?>
