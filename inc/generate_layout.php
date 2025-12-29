<?php
$items = [];

function add_stall($id, $r, $c, $w, $h, $type='stall') {
    global $items;
    $area = $w * $h;
    $price = $area * 11500;
    $items[] = [
        "id" => $id,
        "r" => $r, "c" => $c, "h" => $h, "w" => $w,
        "area" => $area,
        "dim" => "{$w}x{$h}",
        "price" => $price,
        "type" => $type
    ];
}

// --- LEFT COLUMN (ES) ---
// x=1, w=6. y starts 11. h=4.
$es_y = 11;
for ($i = 8; $i > 0; $i--) {
    if ($i == 5) $es_y += 2; // Fire Exit Gap
    add_stall("3.ES $i", $es_y, 1, 6, 4);
    $es_y += 4;
}

// 3.BS 1 (Bottom Left) - Adjusted to match bottom row
// add_stall("3.BS 1", 45, 1, 6, 6); // Removed, added at bottom

// --- BLOCK A ---
// x=9, w=6.
// 3.A 1-10 (3x3)
$pairs_a = [[5,6], [4,7], [3,8], [2,9], [1,10]];
$y = 11;
foreach ($pairs_a as $pair) {
    add_stall("3.A {$pair[0]}", $y, 9, 3, 3);
    add_stall("3.A {$pair[1]}", $y, 12, 3, 3);
    $y += 3;
}

add_stall("3.AS 6", 26, 9, 6, 6);
add_stall("3.AS 5", 32, 9, 6, 6);
add_stall("3.FS 4", 38, 9, 3, 6);
add_stall("3.FS 5", 38, 12, 3, 6);
add_stall("3.FS 3", 44, 9, 3, 6);
add_stall("3.FS 6", 44, 12, 3, 6);
add_stall("3.AS 4", 50, 9, 6, 6);

// --- BLOCK B ---
// x=17
add_stall("3.GS 1", 11, 17, 3, 4);
add_stall("3.GS 2", 11, 20, 3, 4);

$pairs_b = [[6,7], [5,8], [4,9], [3,10], [2,11], [1,12]];
$y = 15;
foreach ($pairs_b as $pair) {
    add_stall("3.B {$pair[0]}", $y, 17, 3, 3);
    add_stall("3.B {$pair[1]}", $y, 20, 3, 3);
    $y += 3;
}

add_stall("3.AS 8", 33, 17, 6, 6);
add_stall("3.AS 7", 39, 17, 6, 6);
add_stall("3.FS 8", 45, 17, 3, 6);
add_stall("3.FS 9", 45, 20, 3, 6);
add_stall("3.FS 7", 51, 17, 3, 6);
add_stall("3.FS 10", 51, 20, 3, 6);
add_stall("3.AS 3", 57, 17, 6, 6);

// --- BLOCK C ---
// x=25
$pairs_c = [[6,7], [5,8], [4,9], [3,10], [2,11], [1,12]];
$y = 11;
foreach ($pairs_c as $pair) {
    add_stall("3.C {$pair[0]}", $y, 25, 3, 3);
    add_stall("3.C {$pair[1]}", $y, 28, 3, 3);
    $y += 3;
}

add_stall("3.AS 10", 29, 25, 6, 6);
add_stall("3.AS 9", 35, 25, 6, 6);
add_stall("3.FS 12", 41, 25, 3, 6);
add_stall("3.FS 13", 41, 28, 3, 6);
add_stall("3.FS 11", 47, 25, 3, 6);
add_stall("3.FS 14", 47, 28, 3, 6);
add_stall("3.AS 2", 53, 25, 6, 6);

// --- BLOCK D ---
// x=33
$pairs_d = [[6,7], [5,8], [4,9], [3,10], [2,11], [1,12]];
$y = 11;
foreach ($pairs_d as $pair) {
    add_stall("3.D {$pair[0]}", $y, 33, 3, 3);
    add_stall("3.D {$pair[1]}", $y, 36, 3, 3);
    $y += 3;
}

add_stall("3.AS 12", 29, 33, 6, 6);
add_stall("3.AS 11", 35, 33, 6, 6);
add_stall("3.FS 16", 41, 33, 3, 6);
add_stall("3.FS 17", 41, 36, 3, 6);
add_stall("3.FS 15", 47, 33, 3, 6);
add_stall("3.FS 18", 47, 36, 3, 6);
add_stall("3.AS 1", 53, 33, 6, 6);

// --- BLOCK E ---
// x=41
$y = 11;
for ($i = 6; $i > 0; $i--) {
    add_stall("3.E $i", $y, 41, 6, 4);
    $y += 4;
}

$y += 2; // Fire Exit
for ($i = 19; $i < 24; $i++) {
    add_stall("3.FS $i", $y, 41, 6, 3);
    $y += 3;
}

add_stall("3.DS 1", $y, 41, 6, 6);

// --- TOP ROW (F) ---
// y=5, h=6, w=3
$x = 25;
for ($i = 7; $i > 0; $i--) {
    add_stall("3.F $i", 5, $x, 3, 6);
    $x += 3;
}

// --- BOTTOM ROW EXTRAS ---
// 3.BS 1 (Green) - 3.FS 2 (Yellow) - 3.FS 1 (Yellow)
// Aligning with bottom of blocks
// Block A ends at 56. Block B ends at 63.
// Let's put bottom row at y=64
add_stall("3.BS 1", 64, 1, 8, 4); // Green corner
add_stall("3.FS 2", 64, 9, 6, 3); // Yellow
add_stall("3.FS 1", 64, 15, 6, 3); // Yellow

add_stall("3.FS 25", 64, 33, 6, 3);
add_stall("3.FS 24", 64, 39, 6, 3);
add_stall("3.DS 1", 64, 45, 3, 3); // Corner

// Features
add_stall("SEMINAR HALL", 1, 1, 14, 10, "feature");
add_stall("STAGE", 1, 1, 4, 10, "feature");
add_stall("INFO", 1, 16, 4, 4, "service");
add_stall("FREIGHT", 1, 20, 4, 4, "entry");
add_stall("WASHROOMS", 1, 24, 8, 4, "washroom");

echo json_encode($items, JSON_PRETTY_PRINT);
?>