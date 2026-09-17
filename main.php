<?php
require_once("functions.php");

// Vytvorenie tabuľky ak neexistuje
getDB()->exec("CREATE TABLE IF NOT EXISTS {$tableName} (id INTEGER PRIMARY KEY UNIQUE, title VARCHAR (500) NOT NULL, link VARCHAR (4500) NOT NULL, pubDate DATETIME NOT NULL)");

?>
<!DOCTYPE html>
<html>

<head>
   <title>Add new things into RSS</title>
   <meta charset='UTF-8'>
   <meta name="csrf-token" content="<?= htmlspecialchars(getCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
   <!--Import materialize.css-->
   <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
   <link type="text/css" rel="stylesheet" href="css/materialize.colors.min.css" media="screen,projection" />
   <link type="text/css" rel="stylesheet" href="css/custom.css" media="screen,projection" />
   <!--Let browser know website is optimized for mobile-->
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <script src="js/jquery-4.0.0.min.js"></script>
   <script src="js/custom.js"></script>
</head>

<body class="blue-grey darken-4 white-text">
   <div class="row gap3">
      <div class="col m5 s9 offset-m2">
         <span id="topDIV">
            <?php
            if (isLoggedIn())
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
      <div class="col l3 hide-on-med-and-down maxHEIGHT25 padding5" id="UploadentriesDIV">
         <div class="row horizontal-center">
            <div class='col l11 wordWrap align-left'>
               <span class='bold fontSizeLarge'>Uploaded files</span>
            </div>
         </div>
         <div class="row horizontal-center">
            <div class='col l11 wordWrap align-left'>
               &nbsp;
            </div>
         </div>
         <span id="showUploads">
            <?php
            if (isLoggedIn())
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
            if (isLoggedIn())
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
            success: function(response) {
               $(targetElement).html(response);
            },
            error: function(xhr, status, error) {
               console.error(xhr.responseText);
            }
         });
      }

      function elementsToReload() {
         reloadContent('showUploads.php', '#showUploads');
         reloadContent('showDBentries.php', '#showDBentries');
      }

      $(document).ready(function() {
         let reloadTimeInMilliseconds = <?php echo (int)($reloadTimeInMilliseconds ?? 10000); ?>;

         $.ajaxSetup({
            data: {
               csrf_token: $('meta[name="csrf-token"]').attr('content')
            }
         });

         // Function to handle click event on remove links
         function handleRemoveLinkClick(e) {
            e.preventDefault();

            var fileName = $(this).data("filename");
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.post("removeFile.php", {
                  fileName: fileName,
                  csrf_token: csrfToken
               })
               .done(function(response) {
                  console.log("Response", response);
                  reloadContent('showUploads.php', '#showUploads');
               })
               .fail(function(xhr, status, error) {
                  console.error(
                     "(handleRemoveLinkClick) Error removing file:",
                     xhr.status,
                     error,
                     xhr.responseText
                  );
               });
         }

         function handleRemoveDBentryClick(e) {
            e.preventDefault();

            var entryID = $(this).data("id");
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.post("removeDBentry.php", {
                  id: entryID,
                  csrf_token: csrfToken
               })
               .done(function(response) {
                  console.log("Response", response);
                  reloadContent('showDBentries.php', '#showDBentries');
               })
               .fail(function(xhr, status, error) {
                  console.error(
                     "(handleRemoveDBentryClick) Error removing DB entry:",
                     xhr.status,
                     error,
                     xhr.responseText
                  );
               });
         }

         // Event handlers
         $(document).on('click', '.removeLink', handleRemoveLinkClick);
         $(document).on('click', '.removeDBentry', handleRemoveDBentryClick);
         $(document).on("keypress", "input", function(e) {
            if (e.which == 13) {
               e.preventDefault();
               jQuery(this).blur();
               jQuery('#LOGINsubmit').focus().click();
            }
         });

         // Interval reload
         setInterval(function() {
            elementsToReload();
         }, reloadTimeInMilliseconds);
      });

      function addToDB(entryName, entryLink) {
         var csrfToken = $('meta[name="csrf-token"]').attr('content');

         $.post("addDBentry.php", {
               entryName: entryName,
               entryLink: entryLink,
               csrf_token: csrfToken
            })
            .done(function(response) {
               console.log("Response", response);
               reloadContent('showDBentries.php', '#showDBentries');
            })
            .fail(function(xhr, status, error) {
               console.error("(addToDB) Error adding entry:", error);
            });
      }

      // ADD MAGNET/URL
      function submitForm() {
         var form = document.forms["linkForm"];
         var entryName = form["linkForm_name"].value;
         var entryLink = form["linkForm_link"].value;
         var csrfToken = $('meta[name="csrf-token"]').attr('content');

         var nameErrorDiv = document.getElementById("linkForm_name_DIV");
         var linkErrorDiv = document.getElementById("linkForm_link_DIV");

         if (form.checkValidity()) {
            $.post("addDBentry.php", {
                  entryName: entryName,
                  entryLink: entryLink,
                  csrf_token: csrfToken
               })
               .done(function(response) {
                  console.log("Response", response);
                  reloadContent('showDBentries.php', '#showDBentries');
                  form.reset();
                  if (nameErrorDiv) nameErrorDiv.classList.remove("error");
                  if (linkErrorDiv) linkErrorDiv.classList.remove("error");
               })
               .fail(function(xhr, status, error) {
                  console.error("(submitForm) Error adding entry:", error);
               });
         } else {
            console.log("Something is missing...");
            if (nameErrorDiv) nameErrorDiv.classList.toggle("error", entryName === "");
            if (linkErrorDiv) linkErrorDiv.classList.toggle("error", entryLink === "");
         }
      }

      // FILE UPLOAD (Spracovanie JSON odpovede)
      $('#fileInput').change(function() {
         var form = document.forms["fileUploader"];
         var formData = new FormData();
         var fileInput_length = $(this)[0].files.length;
         var csrfToken = $('meta[name="csrf-token"]').attr('content');

         if (fileInput_length > 0) {
            for (var i = 0; i < fileInput_length; i++) {
               formData.append("uploaded_file[]", $(this)[0].files[i]);
            }
            // Pridanie CSRF tokenu do FormData
            formData.append("csrf_token", csrfToken);
         } else {
            return;
         }

         $.ajax({
            url: 'uploadFile.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
               console.log("Upload Response:", response);

               if (response.files && response.files.length > 0) {
                  response.files.forEach(function(file) {
                     var normalizedName = file.normalized_name;
                     var fullLink = "<?php echo "{$linkURL}/uploads/"; ?>" + normalizedName;

                     addToDB(normalizedName, fullLink);
                  });

                  reloadContent('showUploads.php', '#showUploads');
                  if (form) form.reset();
               }

               if (response.errors && response.errors.length > 0) {
                  alert("Chyba pri uploade:\n" + response.errors.join("\n"));
               }
            },
            error: function(xhr, status, error) {
               console.error("Upload Failed:", xhr.responseText || error);
               alert("Nastala chyba pri komunikácii so serverom.");
            }
         });
      });

      // Tooltipy
      var tooltipElements = document.querySelectorAll('.tooltip');
      tooltipElements.forEach(function(element) {
         var innerHTML = element.textContent.replace(/\s+/g, ' ');
         element.setAttribute('title', innerHTML);
      });
   </script>

</body>

</html>