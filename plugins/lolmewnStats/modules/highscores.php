<?php
$config->setDefault("stats", array("playtime", "joins"));
$config->setDefault("show_name_head", "true");
$config->setDefault("limit", "10");
$config->setDefault("show_number", "true");
$config->setDefault("show_online", "true");

?>

<?php
$showPlayerTitle = $config->get("show_name_head");
$limit = $config->get("limit");
$showNum = $config->get("show_number");
$showOnline = $config->get("show_online");

if ($showOnline == "true")
    $query = new query($plugin->BlueStatsMYQLI);

?>
<div class="row">
    <?php foreach ($config->get("stats") as $statName): ?>
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h2 class="panel-title">Top <?= $limit ?> by <b><?= $plugin->statName($statName) ?></b></h2>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <?php if ($showNum == "true") {
                                    echo "<th>#</th>";
                                } ?>
                            <th><?php if ($showPlayerTitle == "true") {
                                    echo "Player";
                                } ?></th>
                            <?php if ($showOnline == "true") {
                                echo "<th>Status</th>";
                            } ?>
                            <th><?= $plugin->statName($statName) ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($plugin->getAllPlayerStats($statName, $limit) as $id => $stat): ?>
                        <?php
                            if ($statName == "last_join" || $statName == "last_seen") {
                                $statDisplay = secondsToTime(round(time() - ($stat["value"]/1000)));
                            } elseif ($statName == "playtime") {
                                $statDisplay = secondsToTime($stat["value"]);
                            } else {
                                $statDisplay = $stat["value"];
                            }
                            $linkId = $url->useUUID ? $stat["uuid"] : $stat["name"];
                            $linkUrl = $url->player($linkId);

                            $place = $id + 1
                            ?>

							<tr>
                                <?php
                                if ($showNum == "true") {
                                    echo "<td>$place</td>";
                                } ?>
								<td>
									<a href="<?=$linkUrl?>">
                                        <img src="https://minotar.net/helm/<?=$stat["name"]?>/32.png\" alt="">
                                        <?=$stat["name"]?>
                                    </a>
								</td>
                                <?php
                                if ($showOnline == "true") {
                                    if (in_array($stat["name"], $query->onlinePlayers())) {
                                        echo '<td><span class="label label-success">Online</span></td>';
                                    } else {
                                        echo '<td><span class="label label-danger">Offline</span></td>';
                                    }
                                } ?>
								<td>
                                    <?=$statDisplay?>
								</td>
							</tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>