<div class="d-flex justify-content-between mb-2">
    <div><h1><?= $this->name ?></h1></div>
    <div class="pt-1">
        <?php if(IsInRole(array('technologist', 'dev', 'manager'))): ?>
        <form class="form-inline" method="get">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <div class="form-group">
                <label for="from" style="font-size: larger;">от&nbsp;</label>
                <input type="date" id="from" name="from" class="form-control mr-2" value="<?= filter_input(INPUT_GET, 'from') ?>"/>
            </div>
            <div class="form-group">
                <label for="to" style="font-size: larger;">&nbsp;до&nbsp;</label>
                <input type="date" id="to" name="to" class="form-control mr-2" value="<?= filter_input(INPUT_GET, 'to') ?>"/>
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-outline-dark mr-2">Показать&nbsp;<i class="fas fa-desktop"></i></button>
            </div>
            <div class="form-group">
                <a href="?id=<?= filter_input(INPUT_GET, 'id') ?>" class="btn btn-outline-dark">Сбросить</a>
            </div>
        </form>
        <?php elseif(IsInRole(array('storekeeper'))): ?>
        <form class="form-inline" action="<?=APPLICATION ?>/grafik/print.php" target="_blank" method="post">
            <input type="hidden" id="from" name="from" value="<?= $this->dateFrom->format('Y-m-d') ?>" class="print_from" />
            <input type="hidden" id="to" name="to" value="<?= filter_input(INPUT_GET, 'to') ?>" class="print_to" />
            <input type="hidden" id="machine" name="machine" value="<?= $this->machineId ?>"/>
            <input type="hidden" id="name" name="name" value="<?= $this->name ?>"/>
            <input type="hidden" id="user1Name" name="user1Name" value="<?= $this->user1Name ?>"/>
            <input type="hidden" id="user2Name" name="user2Name" value="<?= $this->user2Name ?>"/>
            <input type="hidden" id="userRole" name="userRole" value="<?= $this->userRole ?>"/>
            <input type="hidden" id="hasOrganization" name="hasOrganization" value="<?= $this->hasOrganization ?>"/>
            <input type="hidden" id="hasEdition" name="hasEdition" value="<?= $this->hasEdition ?>"/>
            <input type="hidden" id="hasMaterial" name="hasMaterial" value="<?=$this->hasMaterial ?>"/>
            <input type="hidden" id="hasThickness" name="hasThickness" value="<?=$this->hasThickness ?>"/>
            <input type="hidden" id="hasWidth" name="hasWidth" value="<?=$this->hasWidth ?>"/>
            <input type="hidden" id="hasLength" name="hasLength" value="<?= $this->hasLength ?>"/>
            <input type="hidden" id="hasStatus" name="hasStatus" value="<?= $this->hasStatus ?>"/>
            <input type="hidden" id="hasPrepare" name="hasPrepare" value="<?= $this->hasPrepare ?>"/>
            <input type="hidden" id="hasRoller" name="hasRoller" value="<?= $this->hasRoller ?>"/>
            <input type="hidden" id="hasLamination" name="hasLamination" value="<?= $this->hasLamination ?>"/>
            <input type="hidden" id="hasColoring" name="hasColoring" value="<?= $this->hasColoring ?>"/>
            <input type="hidden" id="hasManager" name="hasManager" value="<?= $this->hasManager ?>"/>
            <input type="hidden" id="hasComment" name="hasComment" value="<?= $this->hasComment ?>"/>
            <input type="hidden" name="print_submit" value="1" />
            <button type="button" class="form-control btn btn-outline-dark" onclick="javascript: this.form.submit();">Печать&nbsp;<i class="fas fa-print"></i></button>
        </form>
        <?php endif; ?>
    </div>
</div>
<table class="table table-bordered typography">
    <thead id="grafik-thead">
        <tr>
            <th></th>
            <th>Дата</th>
            <th>Смена</th>
            <?php if($this->user1Name): ?><th><?= $this->user1Name ?></th><?php endif; ?>
            <?php if($this->user2Name): ?><th><?= $this->user2Name ?></th><?php endif; ?>
            <?php if($this->hasOrganization): ?><th>Заказчик</th><?php endif; ?>
            <?php if($this->hasEdition): ?><th>Наименование</th><?php endif; ?>
            <?php if($this->hasMaterial): ?><th>Марка пленки</th><?php endif; ?>
            <?php if($this->hasThickness): ?><th>Толщина</th><?php endif; ?>
            <?php if($this->hasWidth): ?><th>Ширина</th><?php endif; ?>
            <?php if($this->hasLength): ?><th>Метраж</th><?php endif; ?>
            <?php if($this->hasPrepare): ?><th>Нужно подготовить</th><?php endif; ?>
            <?php if($this->hasRoller): ?><th>Вал</th><?php endif; ?>
            <?php if($this->hasLamination): ?><th>Ламинация</th><?php endif; ?>
            <?php if($this->hasColoring): ?><th>Кр-ть</th><?php endif; ?>
            <?php if($this->hasManager): ?><th>Менеджер</th><?php endif; ?>
            <?php if($this->hasComment): ?><th>Комментарий</th><?php endif; ?>
        </tr>
    </thead>
        <?php
        foreach($this->grafik_dates as $grafik_date) {
            $grafik_date->Show();
        }
        ?>
</table>