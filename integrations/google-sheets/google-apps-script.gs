/**
 * CNC Expo - Google Apps Script Handler
 * Handles stall bookings, visitor registrations, and 2-way status sync.
 * Version: 6.2 - CNC Edition (With Publish Menu)
 */

// CONFIGURATION
var WEBSITE_URL = "https://cablenetconvergence.com"; // Update if different
var API_SECRET = "cnc_xpo_2026_secure_sync"; // Must match WordPress secret

/**
 * ⚠️ ONE-TIME SETUP: Run this function manually to fix permissions
 * 1. Select 'fixPermissions' from the dropdown above.
 * 2. Click 'Run'.
 * 3. Accept the permissions in the popup.
 */
function fixPermissions() {
  // This forces Google to ask for permission to connect to external websites
  UrlFetchApp.fetch("https://www.google.com");
  SpreadsheetApp.getActiveSpreadsheet().toast("✅ Permissions fixed! You can now use the menu.");
}

/**
 * Create Custom Menu on Open
 */
function onOpen() {
  var ui = SpreadsheetApp.getUi();
  ui.createMenu('CNC Expo')
      .addItem('🚀 Publish Data to Website', 'publishAllBookings')
      .addToUi();
}

/**
 * Publish ALL Bookings to Website
 */
function publishAllBookings() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName('Bookings');
  if (!sheet) {
    SpreadsheetApp.getUi().alert('Error: "Bookings" sheet not found.');
    return;
  }
  
  var ui = SpreadsheetApp.getUi();
  var response = ui.alert('Confirm Publish', 'Are you sure you want to sync ALL booking statuses to the website? This may take a moment.', ui.ButtonSet.YES_NO);
  
  if (response !== ui.Button.YES) return;
  
  var data = sheet.getDataRange().getValues();
  var headers = data[0];
  var rows = data.slice(1);
  var successCount = 0;
  var errorCount = 0;
  var errorMessages = [];
  
  // Show toast
  ss.toast("Starting sync...", "CNC Expo", 5);
  
  // Loop through rows (start from index 0 of rows array, which is row 2 of sheet)
  for (var i = 0; i < rows.length; i++) {
    var rowData = rows[i];
    var rowIndex = i + 2; // 1-based index + header row
    
    // Column B (Index 1) is Booking ID
    var bookingId = rowData[1];
    // Column H (Index 7) is Status
    var status = rowData[7];
    
    if (bookingId && status) {
      var result = syncStatusToWebsite(bookingId, status, rowIndex, sheet, true); // true = silent mode (no individual toasts)
      if (result.success) {
        successCount++;
      } else {
        errorCount++;
        if (errorMessages.indexOf(result.error) === -1) {
          errorMessages.push(result.error);
        }
      }
    }
  }
  
  var msg = 'Successfully synced: ' + successCount + '\nErrors: ' + errorCount;
  if (errorMessages.length > 0) {
    msg += '\n\nError Details:\n' + errorMessages.join('\n');
  }
  
  ui.alert('Sync Complete', msg, ui.ButtonSet.OK);
}

function doPost(e) {
  try {
    var request = JSON.parse(e.postData.contents);
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    
    // Handle CNC Booking Action (Stall Booking)
    if (request.action === 'create_booking' || request.action === 'booking_created') {
      return handleBooking(ss, request.data);
    }
    
    // Handle Visitor Registration
    if (request.action === 'register_visitor') {
      return handleVisitor(ss, request.data);
    }

    // Handle Booking Deletion
    if (request.action === 'delete_booking') {
      return handleDeletion(ss, request.data);
    }

    // Handle Booking Update
    if (request.action === 'update_booking' || request.action === 'addons_update') {
      return handleUpdate(ss, request.data);
    }
    
    return createResponse(false, "Unknown action");
    
  } catch (error) {
    return createResponse(false, error.toString());
  }
}

/**
 * Handle GET requests (for pulling data to website)
 */
function doGet(e) {
  var action = e.parameter.action;
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  
  if (action === 'get_bookings') {
    return getBookingsData(ss);
  }
  
  return createResponse(false, "Unknown action or missing parameters");
}

function getBookingsData(ss) {
  var sheet = ss.getSheetByName('Bookings');
  if (!sheet) return createResponse(false, "Bookings sheet not found");
  
  var data = sheet.getDataRange().getValues();
  var headers = data[0];
  var rows = data.slice(1);
  
  var results = rows.map(function(row) {
    var obj = {};
    headers.forEach(function(header, i) {
      obj[header] = row[i];
    });
    return obj;
  });
  
  return createResponse(true, "Bookings fetched", { bookings: results });
}

/**
 * TRIGGER: Run this when a cell is edited
 * Syncs status changes back to the website
 * NOTE: Must be set up as an Installable Trigger (From spreadsheet -> On edit)
 */
function installableOnEdit(e) {
  var sheet = e.source.getActiveSheet();
  var range = e.range;
  var col = range.getColumn();
  var row = range.getRow();
  var val = range.getValue();
  
  // Only run on 'Bookings' sheet and 'Status' column (H = 8)
  // And ensure it's not the header row
  if (sheet.getName() === 'Bookings' && col === 8 && row > 1) {
    var bookingId = sheet.getRange(row, 2).getValue(); // Column B is Booking ID
    
    if (bookingId) {
      syncStatusToWebsite(bookingId, val, row, sheet, false);
    }
  }
}

/**
 * Send status update to WordPress
 */
function syncStatusToWebsite(postId, status, row, sheet, silent) {
  var url = WEBSITE_URL + "/wp-json/cnc/v1/update-status";
  
  var payload = {
    "post_id": postId,
    "status": status,
    "secret": API_SECRET
  };
  
  var options = {
    "method": "post",
    "contentType": "application/json",
    "payload": JSON.stringify(payload),
    "muteHttpExceptions": true,
    "headers": {
      "User-Agent": "CNC-Google-Script/1.0"
    }
  };
  
  try {
    var response = UrlFetchApp.fetch(url, options);
    var responseCode = response.getResponseCode();
    var responseText = response.getContentText();
    
    // Try to parse JSON
    var json;
    try {
      json = JSON.parse(responseText);
    } catch (e) {
      // If not JSON, it's likely an HTML error page (404, 500, etc.)
      var errorMsg = "Server Error (" + responseCode + "): " + responseText.substring(0, 100);
      if (!silent) SpreadsheetApp.getActiveSpreadsheet().toast("❌ " + errorMsg);
      return { success: false, error: errorMsg };
    }
    
    if (json.success) {
      sheet.getRange(row, 8).setBackground("#d4edda"); // Green tint for success
      if (!silent) SpreadsheetApp.getActiveSpreadsheet().toast("✅ Status synced to website!");
      return { success: true };
    } else {
      var msg = json.message || json.code || "Unknown error";
      sheet.getRange(row, 8).setBackground("#f8d7da"); // Red tint for error
      if (!silent) SpreadsheetApp.getActiveSpreadsheet().toast("❌ Sync failed: " + msg);
      return { success: false, error: msg };
    }
    
  } catch (error) {
    sheet.getRange(row, 8).setBackground("#f8d7da");
    if (!silent) SpreadsheetApp.getActiveSpreadsheet().toast("❌ Network Error: " + error.toString());
    return { success: false, error: error.toString() };
  }
}

function handleBooking(ss, data) {
  var sheetName = 'Bookings';
  var sheet = ss.getSheetByName(sheetName);
  
  if (!sheet) {
    sheet = ss.insertSheet(sheetName);
    setupBookingHeaders(sheet);
  }
  
  if (sheet.getLastRow() === 0) {
    setupBookingHeaders(sheet);
  }
  
  var row = [
    data.timestamp || new Date(),
    data.wp_post_id,
    data.stall_id,
    data.company,
    data.contact || data.name, // Support both field names
    data.email,
    data.phone,
    data.status, // Column H
    'Website' // Source
  ];
  
  sheet.appendRow(row);
  
  // Add Dropdown Validation to the new Status cell
  var lastRow = sheet.getLastRow();
  var statusCell = sheet.getRange(lastRow, 8);
  var rule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Approved', 'Rejected', 'Pending'], true)
    .setAllowInvalid(false)
    .build();
  statusCell.setDataValidation(rule);
  
  return createResponse(true, 'Booking recorded', {
    sheet: sheetName,
    row: lastRow
  });
}

function handleVisitor(ss, data) {
  var sheetName = 'Visitors registration';
  var sheet = ss.getSheetByName(sheetName);
  
  if (!sheet) {
    sheet = ss.insertSheet(sheetName);
    setupVisitorHeaders(sheet);
  }
  
  if (sheet.getLastRow() === 0) {
    setupVisitorHeaders(sheet);
  }
  
  var row = [
    data.timestamp || new Date(),
    data.name,
    data.email,
    data.phone,
    data.company,
    data.designation,
    data.city,
    data.interest,
    'Website'
  ];
  
  sheet.appendRow(row);
  
  return createResponse(true, 'Visitor registration recorded', {
    sheet: sheetName,
    row: sheet.getLastRow()
  });
}

function handleDeletion(ss, data) {
  var sheet = ss.getSheetByName('Bookings');
  if (!sheet) return createResponse(false, "Bookings sheet not found");
  
  var postId = data.wp_post_id;
  if (!postId) return createResponse(false, "Missing Post ID");
  
  var dataRange = sheet.getDataRange();
  var values = dataRange.getValues();
  
  // Iterate backwards to avoid index shifting issues if we were deleting multiple (though here we stop after one)
  for (var i = values.length - 1; i >= 1; i--) {
    // Column B is Booking ID (index 1)
    if (values[i][1] == postId) {
      sheet.deleteRow(i + 1);
      return createResponse(true, "Booking row deleted", { row: i + 1 });
    }
  }
  
  return createResponse(false, "Booking not found in sheet");
}

function handleUpdate(ss, data) {
  var sheet = ss.getSheetByName('Bookings');
  if (!sheet) return createResponse(false, "Bookings sheet not found");
  
  var postId = data.wp_post_id;
  if (!postId) return createResponse(false, "Missing Post ID");
  
  var dataRange = sheet.getDataRange();
  var values = dataRange.getValues();
  
  for (var i = 1; i < values.length; i++) {
    if (values[i][1] == postId) { // Column B is Booking ID
       // Update columns if data provided
       if (data.company) sheet.getRange(i + 1, 4).setValue(data.company);
       if (data.contact) sheet.getRange(i + 1, 5).setValue(data.contact);
       if (data.email) sheet.getRange(i + 1, 6).setValue(data.email);
       if (data.phone) sheet.getRange(i + 1, 7).setValue(data.phone);
       
       return createResponse(true, "Booking updated", { row: i + 1 });
    }
  }
  return createResponse(false, "Booking not found for update");
}

function setupBookingHeaders(sheet) {
  var headers = ['Timestamp', 'Booking ID (WP)', 'Stall ID', 'Company Name', 'Contact Person', 'Email', 'Phone', 'Status', 'Source'];
  sheet.getRange(1, 1, 1, headers.length).setValues([headers]).setFontWeight('bold').setBackground('#E0E0E0');
  sheet.setFrozenRows(1);
}

function setupVisitorHeaders(sheet) {
  var headers = ['Timestamp', 'Name', 'Email', 'Phone', 'Company', 'Designation', 'City', 'Interest', 'Source'];
  sheet.getRange(1, 1, 1, headers.length).setValues([headers]).setFontWeight('bold').setBackground('#E0E0E0');
  sheet.setFrozenRows(1);
}

function createResponse(success, message, data) {
  return ContentService.createTextOutput(JSON.stringify({
    result: success ? 'success' : 'error',
    message: message,
    data: data || {}
  })).setMimeType(ContentService.MimeType.JSON);
}
