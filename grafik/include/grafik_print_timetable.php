<h1><?=$this->name ?></h1>
<table class="table table-bordered print">
    <tr>
        <th></th>
        <th>Дата</th>
        <th>Смена</th>
        <?php if($this->user1Name != ''): ?> <th><?=$this->user1Name ?></th> <?php endif; ?>
        <?php if($this->user2Name != ''): ?> <th><?=$this->user2Name ?></th> <?php endif; ?>
        <?php if($this->hasOrganization): ?> <th>Заказчик</th> <?php endif; ?>
        <?php if($this->hasEdition): ?> <th>Наименование</th> <?php endif; ?>
        <?php if($this->hasLength): ?> <th>Метраж</th> <?php endif; ?>
        <?php if($this->hasRoller): ?> <th>Вал</th> <?php endif; ?>
        <?php if($this->hasLamination): ?> <th>Ламинация</th> <?php endif; ?>
        <?php if($this->hasColoring): ?> <th>Кр-ть</th> <?php endif; ?>
        <?php if($this->hasManager): ?> <th>Менеджер</th> <?php endif; ?>
        <?php if($this->hasComment): ?> <th>Комментарий</th> <?php endif; ?>
    </tr>
    <?php
    foreach($this->grafik_dates as $grafik_date) {
        $grafik_date->Print();
    }
    ?>
</table>