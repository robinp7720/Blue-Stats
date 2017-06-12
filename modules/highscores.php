<?php

$render = function($module, $plugin, $stat) {
    $info = $plugin->database['stats'][$stat];

    $output = "<div class='col-md-6'><h4>$info[name]</h4>";

    $table = new Table();

    $aggregateID = "";

    // Get aggregate stat id
    foreach ($info["values"] as $id => $info) {
        if ($info['aggregate']) {
            $aggregateID = $id;
            break;
        }
    }

    $stats = $plugin->stats->statList($stat, 10);

    if (!isset($stats) || empty($stats))
        return FALSE;

    foreach ($stats as $row) {
        // Get username of player
        if ($plugin->database['identifier'] != 'name')
            $username = $plugin->player->getName($row['id']);
        if ($plugin->database['identifier'] != 'uuid')
            $uuid = $plugin->player->getUUID($row['id']);
        if (!isset($uuid) || empty($uuid))
            $uuid = $row['id'];
        if (!isset($username) || empty($username))
            $username = $row['id'];

        if ($this->bluestats->url->useUUID) {
            $name = "<a href=\"" . $module->bluestats->url->player($uuid) . "\"><img src=\"https://minotar.net/helm/$username/32.png\" alt=\"\"> {$username}</a>";
        }
        else {
            $name = "<a href=\"" . $module->bluestats->url->player($username) . "\"><img src=\"https://minotar.net/helm/$username/32.png\" alt=\"\"> {$username}</a>";
        }

        // Format according to datatype of value
        switch ($plugin->database['stats'][$stat]["values"][$aggregateID]["dataType"]) {
            case "date":
                $row['aggregate'] = date('H:i m-d-y',$row['aggregate']);
                break;
            case "time":
                $row['aggregate'] = secondsToTime($row['aggregate']);
                break;
            default:
                break;
        }

        $table->addRecord(
            $name,
            $row['aggregate']
        );
    }
    $table->makeHeader("Player", $info['name']);

    $output .= $table->tableToHTML(FALSE);
    $output .= "</div>";

    return $output;

};

/** @var module $this */
foreach ($this->bluestats->plugins as $plugin) {
    /** @var \BlueStats\API\plugin $plugin */
    if (!$plugin::$isMySQLplugin)
        continue;

    echo "<h2>{$plugin->name}</h2>";

    if (!isset($plugin->database['groups'])) $plugin->database['groups'] = [];

    foreach ($plugin->database['groups'] as $groupId => $group) {
        echo "<h3>{$group['name']}</h3>";
        echo "<div class='row'>";
        foreach ($group['stats'] as $stat) {
            echo $render($this, $plugin, $stat);
        }
        echo "</div>";
    }

    echo "<div class='row'>";

    foreach ($plugin->database['stats'] as $stat => $info) {
        // Set default stat options
        if (!isset($info['display'])) $info['display'] = TRUE;
        if (!$info['display']) break;

        echo $render($this, $plugin, $stat);
    }
    echo "</div>";
}