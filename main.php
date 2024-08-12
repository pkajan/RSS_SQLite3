<?php
require_once ("functions.php");

$db->exec("create table if not exists $tableName(id INTEGER PRIMARY KEY UNIQUE, title VARCHAR (250) NOT NULL, link VARCHAR (2500) NOT NULL, pubDate DATETIME NOT NULL)");
/* will create empty table, if doesnt exist */

session_start();
$SUCCESS = FALSE;
if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == sha256($json_data['pwd_hash'])) {
  $SUCCESS = TRUE;
}

?>
<!DOCTYPE html>
<html>

<head>
   <title>Add new things into RSS</title>
   <meta charset='UTF-8'>
   <!--Import Google Icon Font-->
   <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->
   <!--Import materialize.css-->
   <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
   <link type="text/css" rel="stylesheet" href="css/custom.css" media="screen,projection" />
   <!--Let browser know website is optimized for mobile-->
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <script src="js/jquery-3.7.1.min.js"></script>
   <script src="js/custom.js"></script>
</head>

<body class="blue-grey darken-4 white-text">
   <div class="row gap3">
      <div class="col m5 s9 offset-m2">
         <span id="topDIV">
            <?php
            if ($SUCCESS)
              include 'topDIV.php';
            ?>
         </span>
      </div>
      <div class="col m5 s12 center-vertical padding3">
         <span id="loginDIV">
            <?php
            include 'loginDIV.php';
            ?>
         </span>
      </div>
   </div>
   <div class="divider"></div>
   <div class="row">
      <div class="col m2 hide-on-med-and-down maxHEIGHT25 padding5" id="UploadentriesDIV">
         <div class="row horizontal-center">
            <div class='col s11 wordWrap align-left'>
               <span class='bold fontSizeLarge'>Uploaded files</span>
            </div>
         </div>
         <div class="row horizontal-center">
            <div class='col s11 wordWrap align-left'>
               &nbsp;
            </div>
         </div>
         <span id="showUploads">
            <?php
            if ($SUCCESS)
              include 'showUploads.php';
            ?>
         </span>
      </div>
      <div class="col s12 m9 padding10 maxHEIGHT25" id="DBentriesDIV">
         <div class="row horizontal-center gap5">
            <div class="col s1 bold">ID</div>
            <div class="col s5 bold">Title</div>
            <div class="col s3 bold">Link</div>
            <div class="col s2 bold">Date</div>
            <div class="col s1 bold">Delete</div>
         </div>
         <span id="showDBentries">
            <?php
            if ($SUCCESS)
              include 'showDBentries.php';
            ?>
         </span>
      </div>
   </div>
   <!--JavaScript at end of body for optimized loading-->
   <script type="text/javascript" src="js/materialize.min.js"></script>
   <script>
      function reloadContent(url, targetElement) {
         $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
               $(targetElement).html(response);
            },
            error: function (xhr, status, error) {
               console.error(xhr.responseText);
            }
         });
      }

      function elementsToReload() {
         reloadContent('showUploads.php', '#showUploads');
         reloadContent('showDBentries.php', '#showDBentries');
      }

      function sleep(milliseconds) {
         var start = new Date().getTime();
         for (var i = 0; i < 1e7; i++) {
            if ((new Date().getTime() - start) > milliseconds) {
               break;
            }
         }
      }

      $(document).ready(function () {
         let reloadTimeInMilliseconds = <?php echo $reloadTimeInMilliseconds ?>;

         // Function to handle click event on remove links
         function handleRemoveLinkClick(e) {
            e.preventDefault(); // Prevent the default action of the link

            // Retrieve the value of data-filename attribute
            var fileName = $(this)[0].attributes[2].value;

            // Send AJAX request to remove the file
            $.post("removeFile.php", {
               fileName: fileName
            })
               .done(function (response) {
                  // Handle successful response here
                  console.log("Response", response);
                  reloadContent('showUploads.php', '#showUploads');
               })
               .fail(function (xhr, status, error) {
                  // Handle errors here
                  console.error("(handleRemoveLinkClick) Error removing file:", error);
               });
         }

         function handleRemoveDBentryClick(e) {
            e.preventDefault(); // Prevent the default action of the link

            // Retrieve the value of data-filename attribute
            var entryID = $(this)[0].attributes[2].value;

            // Send AJAX request to remove the file
            $.post("removeDBentry.php", {
               id: entryID
            })
               .done(function (response) {
                  // Handle successful response here
                  console.log("Response", response);
                  reloadContent('showDBentries.php', '#showDBentries');
               })
               .fail(function (xhr, status, error) {
                  // Handle errors here
                  console.error("(handleRemoveDBentryClick) Error removing file:", error);
               });
         }


         // Attach click event handler to elements with class .removeLink
         $(document).on('click', '.removeLink', handleRemoveLinkClick);
         $(document).on('click', '.removeDBentry', handleRemoveDBentryClick);
         $(document).on("keypress", "input", function (e) {
            if (e.which == 13) {
               event.preventDefault();
               jQuery(this).blur();
               jQuery('#LOGINsubmit').focus().click();
            }
         });

         // Reload content initially and every X seconds for uploads
         setInterval(function () {
            elementsToReload();
         }, reloadTimeInMilliseconds);
      });

      // ADD TO DB
      function addToDB(entryName, entryLink) {
         $.post("addDBentry.php", {
            entryName: entryName,
            entryLink: entryLink
         })
            .done(function (response) {
               console.log("Response", response);
               reloadContent('showDBentries.php', '#showDBentries');
               form.reset();
               nameErrorDiv.classList.remove("error");
               linkErrorDiv.classList.remove("error");
            })
            .fail(function (xhr, status, error) {
               console.error("(addToDB) Error adding entry:", error);
            });
      }

      // ADD MAGNET/URL
      function submitForm() {
         var form = document.forms["linkForm"];
         var entryName = form["linkForm_name"].value;
         var entryLink = form["linkForm_link"].value;

         var nameErrorDiv = document.getElementById("linkForm_name_DIV");
         var linkErrorDiv = document.getElementById("linkForm_link_DIV");

         if (form.checkValidity()) {
            $.post("addDBentry.php", {
               entryName: entryName,
               entryLink: entryLink
            })
               .done(function (response) {
                  console.log("Response", response);
                  reloadContent('showDBentries.php', '#showDBentries');
                  form.reset();
                  nameErrorDiv.classList.remove("error");
                  linkErrorDiv.classList.remove("error");
               })
               .fail(function (xhr, status, error) {
                  console.error("(submitForm) Error adding entry:", error);
               });
         } else {
            console.log("Something is missing...");
            nameErrorDiv.classList.toggle("error", entryName === "");
            linkErrorDiv.classList.toggle("error", entryLink === "");
         }
      }

      // LOGIN
      function submitLoginLogout() {
         var form = document.forms["loginForm"];
         var LOGINpassword = form["LOGINpassword"].value;

         $.post("login.php", {
            LOGINpassword: LOGINpassword
         })
            .done(function (response) {
               console.log("Response", response);
               form.reset();
               setTimeout(function () {
                  elementsToReload();
                  reloadContent('loginDIV.php', '#loginDIV');
                  reloadContent('topDIV.php', '#topDIV');
               }, 1000);
            })
            .fail(function (xhr, status, error) {
               console.error("(submitLoginLogout) Error adding entry:", error);
            });
      }

      // FILE UPLOAD
      $('#fileInput').change(function () {
         var form = document.forms["fileUpladator"];
         var formData = new FormData();
         var fileInput_length = $(this)[0].files.length;
         if (fileInput_length > 0) {
            for (var i = 0; i < fileInput_length; i++) {
               formData.append("uploaded_file[]", $(this)[0].files[i]);
            }
         }

         $.ajax({
            url: 'uploadFile.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
               console.log("Response", response);
               if (response.includes("Sorry")) {
                  alert(response);
               } else {
                  var myArray = response.split(";;;").filter(n => n);
                  myArray.forEach((response) => {
                     $.post("addDBentry.php", {
                        entryName: response,
                        entryLink: "<?php echo "{$linkURL}/uploads/"; ?>" + response
                     })
                        .done(function (response) {
                           console.log("Response", response);
                           reloadContent('showDBentries.php', '#showDBentries');
                           form.reset();
                        })
                        .fail(function (xhr, status, error) {
                           console.error("Error adding entry:", error);
                        });
                     sleep(500);
                  });
               }

               // Handle success response
            },
            error: function (xhr, status, error) {
               console.error(xhr.responseText);
               // Handle error response
            }
         });
      });

      // Get all elements with the 'tooltip' class
      var tooltipElements = document.querySelectorAll('.tooltip');

      // Iterate over each element to set tooltip content
      tooltipElements.forEach(function (element) {
         var innerHTML = element.textContent.replace(/\s+/g, ' ');
         element.setAttribute('title', innerHTML);
      });
   </script>

</body>

</html>
