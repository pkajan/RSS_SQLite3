<?php
require_once ("functions.php");
$SUCCESS = FALSE;
if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == sha256($json_data['pwd_hash'])) {
  $SUCCESS = TRUE;
}
if ($SUCCESS) {
  echo '
    <div class="row">
        <div class="col s12 m6">
            <form id="linkForm" class="col s6 m12">
                <div class="row padding10">
                    <div id="linkForm_name_DIV" class="input-field outlined col s12 m12" style="margin: 0 4px;">
                        <i class="material-icons suffix">sort_by_alpha</i>
                        <input id="linkForm_name" class="validate" type="text" placeholder=" " required>
                        <label for="linkForm_name">Name</label>
                    </div>
                </div>
                <div class="row padding10">
                    <div id="linkForm_link_DIV" class="input-field outlined col s12 m12" style="margin: 0 4px;">
                        <i class="material-icons suffix">link</i>
                        <input id="linkForm_link" class="validate" type="text" placeholder=" " required>
                        <label for="linkForm_link">Magnet/URL</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="col s12 m6 center-vertical">
            <div class="row">
                <div class="col s4 offset-s1 m4">
                    <a class="waves-effect waves-light btn bold" onclick="submitForm()">ADD</a>
                </div>
                <div class="col s5 m8 wordWrap">
                    <form id="fileUpladator" enctype="multipart/form-data">
                        <input id="fileInput" type="file" name="uploaded_file" multiple hidden>
                        <a class="waves-effect waves-light btn bold" onclick="document.getElementById(\'fileInput\').click()">
                        <i class="material-icons">upload</i>
                            Select File to UPLOAD
                        <i class="material-icons">upload</i>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>';
}
?>

