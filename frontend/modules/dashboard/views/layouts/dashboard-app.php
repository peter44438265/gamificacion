<!DOCTYPE html>

<?php $this->beginContent('@app/web/themes/gamify/views/layouts/main.php'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12 col-12">
        <pan>panel</span>
        <pan>panel2</span>
        <pan>panel3</span>
    </div>
  </div>
  <div class = "row">
      <div class = "col-12">
          <div class = "card">
              <div class="card-body">
                  <?= $content; ?>
              </div>
          </div>
      </div>
  </div>
</div>
<?php $this->endContent(); ?>