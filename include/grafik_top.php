<div class="d-flex justify-content-between mb-2">
    <div class="p-1">
        <h1><?= $this->name ?></h1>
    </div>
    <div class="p-1">
        <?php if(IsInRole('technologist', 'manager-senior')): ?>
        <div class="d-flex justify-content-end mb-auto">
            <div class="p-1">
                <form class="form-inline">
                    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                    <div class="form-group">
                        <label for="from">от&nbsp;</label>
                        <input type="date" id="from" name="from" class="form-control" value="<?= filter_input(INPUT_GET, 'from') ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="to">&nbsp;до&nbsp;</label>
                        <input type="date" id="to" name="to" class="form-control" value="<?= filter_input(INPUT_GET, 'to') ?>"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="form-control btn btn-light">Показать&nbsp;<i class="fas fa-desktop"></i></button>
                    </div>
                </form>
            </div>
            <div class="p-1 ml-1">
                <form class="form-inline" action="<?=APPLICATION ?>/print.php" target="_blank" method="post">
                    <input type="hidden" id="from" name="from" value="<?= $this->dateFrom->format('Y-m-d') ?>" class="print_from" />
                    <input type="hidden" id="to" name="to" value="<?= filter_input(INPUT_GET, 'to') ?>" class="print_to" />
                    <input type="hidden" id="machine" name="machine" value="<?= $this->machineId ?>"/>
                    <input type="hidden" id="name" name="name" value="<?= $this->name ?>"/>
                    <input type="hidden" id="user1Name" name="user1Name" value="<?= $this->user1Name ?>"/>
                    <input type="hidden" id="user2Name" name="user2Name" value="<?= $this->user2Name ?>"/>
                    <input type="hidden" id="userRole" name="userRole" value="<?= $this->userRole ?>"/>
                    <input type="hidden" id="hasEdition" name="hasEdition" value="<?= $this->hasEdition ?>"/>
                    <input type="hidden" id="hasOrganization" name="hasOrganization" value="<?= $this->hasOrganization ?>"/>
                    <input type="hidden" id="hasLength" name="hasLength" value="<?= $this->hasLength ?>"/>
                    <input type="hidden" id="hasStatus" name="hasStatus" value="<?= $this->hasStatus ?>"/>
                    <input type="hidden" id="hasRoller" name="hasRoller" value="<?= $this->hasRoller ?>"/>
                    <input type="hidden" id="hasLamination" name="hasLamination" value="<?= $this->hasLamination ?>"/>
                    <input type="hidden" id="hasColoring" name="hasColoring" value="<?= $this->hasColoring ?>"/>
                    <input type="hidden" id="hasManager" name="hasManager" value="<?= $this->hasManager ?>"/>
                    <input type="hidden" id="hasComment" name="hasComment" value="<?= $this->hasComment ?>"/>
                    <button type="submit" class="form-control btn btn-light" id="print_submit" name="print_submit">Печать&nbsp;<i class="fas fa-print"></i></button>
                </form>
            </div>
            <div class="p-1 ml-1">
                <form action="<?=APPLICATION ?>/csv.php" method="post">
                    <input type="hidden" id="from" name="from" value="<?= $this->dateFrom->format('Y-m-d') ?>"/>
                    <input type="hidden" id="to" name="to" value="<?= $this->dateTo->format('Y-m-d') ?>"/>
                    <input type="hidden" id="machine" name="machine" value="<?= $this->machineId ?>"/>
                    <button type="submit" class="form-control btn btn-light" id="export_submit" name="export_submit">Экспорт&nbsp;<i class="fas fa-file-csv"></i></button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>