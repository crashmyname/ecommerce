<section class="section">
    <div class="section-header">
        <h1>Home</h1>
    </div>

<p><?= $data?></p>
Ini total data User <?= $count?>
<select name="" id="">
    <?php foreach ($user as $data): ?>
        <option value="<?= $data->username?>"><?= $data->username?></option>
    <?php endforeach?>
</select>
</section>