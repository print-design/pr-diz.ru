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