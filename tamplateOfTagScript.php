<?php
if (isset($_POST['tag_name'])) {
?>
    <div class="col-auto" style="margin: 0 1rem 0 3rem;" id="idea<?= $_POST['idea_id'] ?>Tag<?= $_POST['tagIdx'] ?>">
        <div class="row d-flex" style="align-items: center;">
            <div class="col-auto" style="padding: 0 0.25rem 0 0;">
                <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 24px;color: #000000; margin: 0;" id="tagName<?= $_POST['tagIdx'] ?>"><?= $_POST['tag_name'] ?></p>
            </div>
            <div class="col-auto" style="padding: 0;">
                <button type="button" name = "deleteBtnTag<?= $_POST['idea_id'] ?>" class="btn-delete-tag" style="padding: 0;" onclick="deleteAddSuggestTag(<?= $_POST['tagIdx'] ?>, <?= $_POST['idea_id'] ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

<?php
}
