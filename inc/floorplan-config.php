<?php
/**
 * CNC Floor Plan Configuration - Single Source of Truth (SSOT)
 * Defines physical dimensions, grid settings, and status colors for both Backend and Frontend.
 */

// Physical Dimensions (HITEX Hall 3)
// 236.2 ft x 157.5 ft approx -> 72m x 48m
define('CNC_HALL_WIDTH_M', 48);  // Columns
define('CNC_HALL_LENGTH_M', 72); // Rows

// Grid Settings
define('CNC_GRID_COLS', 48);     // 1 unit = 1 meter
define('CNC_GRID_ROWS', 72);
define('CNC_GRID_GAP_PX', 2);    // CSS Gap
define('CNC_CELL_SIZE_PX', 20);  // Base cell size for calculations (Backend visual scaling)

// Colors (BookMyShow Palette)
define('CNC_COLOR_AVAILABLE', '#2ECC71');
define('CNC_COLOR_PENDING',   '#BDC3C7');
define('CNC_COLOR_BOOKED',    '#000000');
define('CNC_COLOR_TEXT',      '#333333');
define('CNC_COLOR_ACTION',    '#E74C3C');
define('CNC_COLOR_EXIT',      '#E74C3C'); // Alias for Action/Exit
define('CNC_COLOR_WASHROOM',  '#3498DB');
define('CNC_COLOR_GAP',       '#F0F0F0');

// Common Area Type Colors
define('CNC_COLOR_FIRE_EXIT',    '#E74C3C');
define('CNC_COLOR_ENTRY',        '#F39C12');
define('CNC_COLOR_EXIT_DOOR',    '#E67E22');
define('CNC_COLOR_FREIGHT',      '#95A5A6');
define('CNC_COLOR_STAIRS',       '#34495E');
define('CNC_COLOR_STAGE',        '#1ABC9C');
define('CNC_COLOR_SERVICE',      '#9B59B6');
define('CNC_COLOR_REGISTRATION', '#9B59B6');
define('CNC_COLOR_LOUNGE',       '#27AE60');
define('CNC_COLOR_STORAGE',      '#7F8C8D');
define('CNC_COLOR_ELECTRICAL',   '#F1C40F');
define('CNC_COLOR_PILLAR',       '#34495E');
define('CNC_COLOR_CORRIDOR',     '#27AE60');
define('CNC_COLOR_FEATURE',      '#FFC107');
define('CNC_COLOR_FOOD_COURT',   '#E74C3C');
define('CNC_COLOR_NETWORKING',   '#2ECC71');

// Common Area Types Array (for reference)
define('CNC_COMMON_AREA_TYPES', serialize([
    'fire-exit', 'entry', 'exit', 'freight-entry', 'washroom', 
    'stairs', 'stage', 'service', 'registration', 'lounge', 
    'storage', 'electrical', 'pillar', 'corridor', 'blank', 'custom-area',
    'food-court', 'networking', 'feature'
]));

/**
 * Get Grid CSS Variables for Frontend/Backend
 */
function cnc_get_floorplan_css_vars() {
    return sprintf(
        ':root {
            --cnc-cols: %d;
            --cnc-rows: %d;
            --cnc-gap: %dpx;
            --cnc-cell: %dpx;
            --cnc-green: %s;
            --cnc-orange: %s;
            --cnc-grey: %s;
            --cnc-red: %s;
            --cnc-blue: %s;
            --cnc-text: %s;
            --cnc-gap-color: %s;
        }',
        CNC_GRID_COLS,
        CNC_GRID_ROWS,
        CNC_GRID_GAP_PX,
        CNC_CELL_SIZE_PX,
        CNC_COLOR_AVAILABLE,
        CNC_COLOR_PENDING,
        CNC_COLOR_BOOKED,
        CNC_COLOR_ACTION,
        CNC_COLOR_WASHROOM,
        CNC_COLOR_TEXT,
        CNC_COLOR_GAP
    );
}
