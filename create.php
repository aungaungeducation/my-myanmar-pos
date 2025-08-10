<?php
require_once('../Vendor/autoload.php');
use Connection\Get\Database;

$data = new Database();
$conn = $data->getconnection();

if ($conn) {
    echo "✅ Database connection success<br>";
} else {
    die("❌ Database connection failed");
}

$agent3d = "CREATE TAVLE IF NOT EXISTS agent_limit(
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
agentprivate TEXT,
number TEXT,
value TEXT,
status TEXT
)";

$conn->query($agent3d);
$userset = "CREATE TABLE IF NOT EXISTS userset(
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
userprivate TEXT,
twod TEXT,
threed TEXT,
toot TEXT
)";

$conn->query($userset);
$agentSET = "CREATE TABLE IF NOT EXISTS agentset (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
agentprivate TEXT,
coins TEXT,
commission TEXT,
secondcommission TEXT,
twod TEXT,
threed TEXT,
toot TEXT,
defaultcommission TEXT
)";

$conn->query($agentSET);
$event = "CREATE TABLE IF NOT EXISTS event (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
agentprivate TEXT,
image TEXT,
link TEXT
)";

$conn -> query($event);

$service = "CREATE TABLE IF NOT EXISTS service (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
agentprivate TEXT,
img1 TEXT,
img2 TEXT,
img3 TEXT,
link1 TEXT,
link2 TEXT,
link3 TEXT,
mobile1 TEXT,
mobile2 TEXT,
email TEXT
)";

$conn->query($service);
$playhis = "CREATE TABLE IF NOT EXISTS reqtwo(
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
userprivate TEXT,
agentprivate TEXT,
hisprivate TEXT,
amount TEXT,
number TEXT,
playtime TEXT,
date TEXT,
hour TEXT,
username TEXT,
profile TEXT,
status TEXT,
wlamount TEXT,
type TEXT
)";

$conn->query($playhis);

$verify = "CREATE TABLE IF NOT EXISTS verify (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
private TEXT,
token TEXT
)";

$conn->query($verify);
$playhis = "CREATE TABLE IF NOT EXISTS playhis(
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
userprivate TEXT,
agentprivate TEXT,
hisprivate TEXT,
amount TEXT,
number TEXT,
playtime TEXT,
date TEXT,
hour TEXT,
username TEXT,
profile TEXT,
status TEXT,
wlamount TEXT,
type text
)";

$conn->query($playhis);

$user = "CREATE TABLE IF NOT EXISTS userauth(
    userid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username TEXT,
    mobile TEXT,
    usercode TEXT,
    commesion TEXT,
    password TEXT,
    noti TEXT,
    balance TEXT,
    android TEXT,
    private TEXT,
    accept TEXT,
    status TEXT,
    panelcode TEXT,
    profile TEXT
)";
$conn->query($user) or die("❌ userauth table error: " . $conn->error);
echo "✅ userauth table created<br>";

// AGENT table
$agent = "CREATE TABLE IF NOT EXISTS agent (
    agentid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agentname TEXT,
    agentmobile TEXT,
    status TEXT,
    profile TEXT,
    private TEXT
)";
$conn->query($agent) or die("❌ agent table error: " . $conn->error);
echo "✅ agent table created<br>";

// CASHIN table
$cashin = "CREATE TABLE IF NOT EXISTS cashin (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ownermobile TEXT NOT NULL,
    paymenttype TEXT NOT NULL,
    paymentlogo TEXT NOT NULL,
    private TEXT NOT NULL,
    amount TEXT NOT NULL,
    usertranid TEXT NOT NULL,
    username TEXT NOT NULL,
    userprofile TEXT NOT NULL,
    slipid TEXT NOT NULL,
    date TEXT NOT NULL,
    month TEXT NOT NULL,
    year TEXT NOT NULL,
    hour TEXT NOT NULL,
    time TEXT NOT NULL,
    ownername TEXT NOT NULL,
    ownerprivate TEXT NOT NULL,
    status TEXT NOT NULL,
    class TEXT NOT NULL,
    insight TEXT NOT NULL
)";
$conn->query($cashin) or die("❌ cashin table error: " . $conn->error);
echo "✅ cashin table created<br>";

// PAYMENT table
$payment = "CREATE TABLE IF NOT EXISTS payment (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    private TEXT NOT NULL,
    wavepay TEXT NOT NULL,
    wavename TEXT NOT NULL,
    kbzpay TEXT NOT NULL,
    kbzname TEXT NOT NULL,
    pincode TEXT NOT NULL
)";
$conn->query($payment) or die("❌ payment table error: " . $conn->error);
echo "✅ payment table created<br>";

$numberWords = [
    'zero','one','two','three','four','five','six','seven','eight','nine',
    'ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen',
    'twenty','twenty_one','twenty_two','twenty_three','twenty_four','twenty_five','twenty_six','twenty_seven','twenty_eight','twenty_nine',
    'thirty','thirty_one','thirty_two','thirty_three','thirty_four','thirty_five','thirty_six','thirty_seven','thirty_eight','thirty_nine',
    'forty','forty_one','forty_two','forty_three','forty_four','forty_five','forty_six','forty_seven','forty_eight','forty_nine',
    'fifty','fifty_one','fifty_two','fifty_three','fifty_four','fifty_five','fifty_six','fifty_seven','fifty_eight','fifty_nine',
    'sixty','sixty_one','sixty_two','sixty_three','sixty_four','sixty_five','sixty_six','sixty_seven','sixty_eight','sixty_nine',
    'seventy','seventy_one','seventy_two','seventy_three','seventy_four','seventy_five','seventy_six','seventy_seven','seventy_eight','seventy_nine',
    'eighty','eighty_one','eighty_two','eighty_three','eighty_four','eighty_five','eighty_six','eighty_seven','eighty_eight','eighty_nine',
    'ninety','ninety_one','ninety_two','ninety_three','ninety_four','ninety_five','ninety_six','ninety_seven','ninety_eight','ninety_nine'
];

// Build CREATE TABLE SQL
$sql = "CREATE TABLE IF NOT EXISTS user_limit (
    pid INT PRIMARY KEY AUTO_INCREMENT,
    userprivate TEXT NOT NULL,
    agentprivate TEXT NOT NULL";

foreach ($numberWords as $col) {
    $sql .= ", `$col` TEXT NOT NULL";
}

$sql .= ");";

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "Table user_data created successfully with 100 columns named zero to ninety_nine.";
} else {
    echo "Error creating table: " . $conn->error;
}
$numberWords = [
    'zero','one','two','three','four','five','six','seven','eight','nine',
    'ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen',
    'twenty','twenty_one','twenty_two','twenty_three','twenty_four','twenty_five','twenty_six','twenty_seven','twenty_eight','twenty_nine',
    'thirty','thirty_one','thirty_two','thirty_three','thirty_four','thirty_five','thirty_six','thirty_seven','thirty_eight','thirty_nine',
    'forty','forty_one','forty_two','forty_three','forty_four','forty_five','forty_six','forty_seven','forty_eight','forty_nine',
    'fifty','fifty_one','fifty_two','fifty_three','fifty_four','fifty_five','fifty_six','fifty_seven','fifty_eight','fifty_nine',
    'sixty','sixty_one','sixty_two','sixty_three','sixty_four','sixty_five','sixty_six','sixty_seven','sixty_eight','sixty_nine',
    'seventy','seventy_one','seventy_two','seventy_three','seventy_four','seventy_five','seventy_six','seventy_seven','seventy_eight','seventy_nine',
    'eighty','eighty_one','eighty_two','eighty_three','eighty_four','eighty_five','eighty_six','eighty_seven','eighty_eight','eighty_nine',
    'ninety','ninety_one','ninety_two','ninety_three','ninety_four','ninety_five','ninety_six','ninety_seven','ninety_eight','ninety_nine'
];

// Build CREATE TABLE SQL
$sql = "CREATE TABLE IF NOT EXISTS panel_limit (
    pid INT PRIMARY KEY AUTO_INCREMENT,
    agentprivate TEXT NOT NULL";

foreach ($numberWords as $col) {
    $sql .= ", `$col` TEXT NOT NULL";
}

$sql .= ");";

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "Table user_data created successfully with 100 columns named zero to ninety_nine.";
} else {
    echo "Error creating table: " . $conn->error;
}


$sql = "CREATE TABLE IF NOT EXISTS paneltime (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
private TEXT NOT NULL,
eight TEXT,
eiththalf TEXT,
nine TEXT,
ninehalf TEXT,
ten TEXT,
tenhalf TEXT,
eleven TEXT,
elevenhalf TEXT,
twelve TEXT,
twelvehalf TEXT,
one TEXT,
onehalf TEXT,
two TEXT,
twohalf TEXT,
three TEXT,
threehalf TEXT,
four TEXT,
fourhalf TEXT,
five TEXT,
fivehalf TEXT,
six TEXT,
sixhalf TEXT,
seven TEXT,
sevenhalf TEXT
)";
if($conn->query($sql) === TRUE) {
    echo "success";
}

$sql = "CREATE TABLE IF NOT EXISTS noti_message (
messageid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
agentprivate TEXT NOT NULL,
header TEXT NOT NULL,
content TEXT NOT NULL,
date TEXT NOT NULL
)";
if($conn->query($sql) === TRUE) {
    echo "success";
}

$numberWords = [
    'zero','one','two','three','four','five','six','seven','eight','nine',
    'ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen',
    'twenty','twenty_one','twenty_two','twenty_three','twenty_four','twenty_five','twenty_six','twenty_seven','twenty_eight','twenty_nine',
    'thirty','thirty_one','thirty_two','thirty_three','thirty_four','thirty_five','thirty_six','thirty_seven','thirty_eight','thirty_nine',
    'forty','forty_one','forty_two','forty_three','forty_four','forty_five','forty_six','forty_seven','forty_eight','forty_nine',
    'fifty','fifty_one','fifty_two','fifty_three','fifty_four','fifty_five','fifty_six','fifty_seven','fifty_eight','fifty_nine',
    'sixty','sixty_one','sixty_two','sixty_three','sixty_four','sixty_five','sixty_six','sixty_seven','sixty_eight','sixty_nine',
    'seventy','seventy_one','seventy_two','seventy_three','seventy_four','seventy_five','seventy_six','seventy_seven','seventy_eight','seventy_nine',
    'eighty','eighty_one','eighty_two','eighty_three','eighty_four','eighty_five','eighty_six','eighty_seven','eighty_eight','eighty_nine',
    'ninety','ninety_one','ninety_two','ninety_three','ninety_four','ninety_five','ninety_six','ninety_seven','ninety_eight','ninety_nine'
];

// Build CREATE TABLE SQL
$sql = "CREATE TABLE IF NOT EXISTS clost_two (
    pid INT PRIMARY KEY AUTO_INCREMENT,
    agentprivate TEXT NOT NULL";

foreach ($numberWords as $col) {
    $sql .= ", `$col` TEXT NOT NULL";
}

$sql .= ");";

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "Table user_data created successfully with 100 columns named zero to ninety_nine.";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
