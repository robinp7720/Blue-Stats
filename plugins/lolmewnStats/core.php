<?php

class lolmewnStats extends MySQLplugin
{
    public $pluginName = "lolmewnStats";
    public $stats;

    public $plugin = array(
        "idColumn" => "uuid",
        "playerNameColumn" => "name",
        "UUIDcolumn" => "uuid",
        "indexTable" => "players",
        "UUIDisID" => true,
        "singleTable" => false,
        "valueColumn" => "value",
        "tables" => [],
        "defaultPrefix" => "Stats_"
    );

    public function onLoad()
    {
        $this->config->setDefault("stats", array(
            "arrows" => "Arrows Shot",
            "beds_entered" => "Beds Entered",
            "blocks_broken" => "Blocks Broken",
            "blocks_placed" => "Blocks Placed",
            "buckets_emptied" => "Buckets Emptied",
            "buckets_filled" => "Buckets Filled",
            "commands_done" => "Commands Done",
            "damage_taken" => "Damage Taken",
            "death" => "Times Died",
            "eggs_thrown" => "Eggs Thrown",
            "fish_caught" => "Fish Caught",
            "items_crafted" => "Items Crafted",
            "items_dropped" => "Items Dropped",
            "items_picked_up" => "Items Picked Up",
            "joins" => "Joins",
            "kill" => "Kills",
            "last_join" => "Last Joined",
            "last_seen" => "Last Seen",
            "move" => "Blocks Traversed",
            "omnomnom" => "Food Eaten",
            "playtime" => "Play Time",
            "pvp" => "PvP Kills",
            "shears" => "Times striped a sheep",
            "teleports" => "Teleports",
            "times_changed_world" => "Worlds Changed",
            "times_kicked" => "Times Kicked",
            "tools_broken" => "Tools Broken",
            "trades" => "Trades",
            "votes" => "Votes",
            "words_said" => "Words Said",
            "xp_gained" => "Xp Gained",
        ));
        $this->stats = $this->config->get("stats");
        foreach ($this->stats as $key => $value) {
            array_push($this->plugin["tables"], $key);
        }

    }

    public function statName($stat)
    {
        if (isset($this->stats[$stat]))
            return $this->stats[$stat];
        return $stat;
    }

    public function getAllPlayerStats($stat, $limit = 0)
    {

        $stmt = $this->mysqli->stmt_init();

        if ($stat == "last_join" || $stat == "last_seen") {
            $sql = "
SELECT
  {$this->prefix}{$this->plugin['indexTable']}.name as name,
  {$this->prefix}{$stat}.{$this->plugin['idColumn']},
  max({$this->prefix}{$stat}.value) as `value` 
FROM {$this->prefix}{$stat}
INNER JOIN `{$this->prefix}{$this->plugin['indexTable']}` on {$this->prefix}{$stat}.{$this->plugin['idColumn']} = {$this->prefix}{$this->plugin['indexTable']}.{$this->plugin['idColumn']}
GROUP BY {$this->prefix}{$this->plugin['indexTable']}.name, {$this->prefix}{$stat}.{$this->plugin['idColumn']}
ORDER BY value Desc";
        } else {
            $sql = "
SELECT 
  {$this->prefix}{$this->plugin['indexTable']}.name as name,
  {$this->prefix}{$stat}.{$this->plugin['idColumn']},
  sum({$this->prefix}{$stat}.value) as `value` 
FROM {$this->prefix}{$stat} 
INNER JOIN `{$this->prefix}{$this->plugin['indexTable']}` on {$this->prefix}{$stat}.{$this->plugin['idColumn']} = {$this->prefix}{$this->plugin['indexTable']}.{$this->plugin['idColumn']}
GROUP BY {$this->prefix}{$this->plugin['indexTable']}.name, {$this->prefix}{$stat}.{$this->plugin['idColumn']}
ORDER BY value Desc";
        }

        if ($limit != 0) {
            $sql = $sql . " LIMIT $limit";
        }

        if ($stmt->prepare($sql)) {
            $stmt->execute();
            echo $stmt->error;
            $output = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // If there is an error log it
            if ($stmt->error && DEBUG)
                print($stmt->error);

            $stmt->close();
            return $output ?: [];
        }
        return [];
    }

    public function getStatSum($stat)
    {

        $stmt = $this->mysqli->stmt_init();

        if ($stat == "last_join" || $stat == "last_seen") {
            return "";
        }
        $sql = "SELECT sum(value) as value FROM {$this->prefix}{$stat}";
        if ($stmt->prepare($sql)) {
            $stmt->execute();
            $stmt->bind_result($output);
            $stmt->fetch();

            // If there is an error log it
            if ($stmt->error && DEBUG)
                print($stmt->error);

            $stmt->close();
            return round($output) ?: "";
        }
        return [];
    }

    public function getStat($stat, $player, $group = TRUE)
    {
        // TODO Clean up getStat function
        $stmt = $this->mysqli->stmt_init();
        if ($group) {
            if ($stat == "last_join" || $stat == "last_seen") {
                $sql = "SELECT max(value) as value FROM {$this->prefix}{$stat} WHERE uuid=?";
            } else {
                $sql = "SELECT sum(value) as value FROM {$this->prefix}{$stat} WHERE uuid=?";
            }
        } else {
            $sql = "SELECT * FROM {$this->prefix}{$stat} WHERE uuid=?";
        }

        if ($group)
            $sql = $sql . " GROUP BY uuid";

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("s", $player);
            $stmt->execute();
            $output = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            if ($group) {
                if (isset($output['value'])) {
                    $output = $output['value'];
                } else {
                    if (isset($output[0])) {
                        $output = $output[0]['value'];
                    }
                }
            }

            // If there is an error log it
            if ($stmt->error && DEBUG)
                print($stmt->error);

            $stmt->close();
            if ($stat == "last_join" || $stat == "last_seen") {
                if (!empty($output)) {
                    $time = time() - ($output/1000);
                    $output = secondsToTime(round($time)) . " ago";
                    #$output = time().":".$output;
                } else {
                    $output = "Never";
                }
            } elseif ($stat == "playtime") {
                $output = secondsToTime($output);
            }
            return $output;
        }
        return false;
    }

    public function getStats($stat, $player, $limit = 0, $extra = "")
    {
        $stmt = $this->mysqli->stmt_init();

        $sql = "SELECT * FROM {$this->prefix}{$stat} WHERE uuid = ?";

        if ($limit != 0) {
            $sql = "$sql $extra LIMIT $limit";
        } else {
            $sql = "$sql $extra";
        }
        if ($stmt->prepare($sql)) {
            $stmt->bind_param("s", $player);
            $stmt->execute();
            $output = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // If there is an error log it
            if ($stmt->error && DEBUG)
                print($stmt->error);

            $stmt->close();
            return $output;
        }
        return false;
    }

    public function getPlayerBlock($player)
    {

        $stmt = $this->mysqli->stmt_init();

        $sql = "SELECT {$this->prefix}blocks_placed.name,{$this->prefix}blocks_placed.data, {$this->prefix}blocks_placed.world,{$this->prefix}blocks_placed.value as \"blocks_placed\", {$this->prefix}blocks_broken.value as \"blocks_broken\" FROM {$this->prefix}blocks_broken INNER JOIN {$this->prefix}blocks_placed on ({$this->prefix}blocks_broken.uuid = {$this->prefix}blocks_placed.uuid and {$this->prefix}blocks_broken.world = {$this->prefix}blocks_placed.world and {$this->prefix}blocks_broken.data = {$this->prefix}blocks_placed.data and {$this->prefix}blocks_broken.name = {$this->prefix}blocks_placed.name ) WHERE {$this->prefix}blocks_broken.uuid = ?";

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("s", $player);
            $stmt->execute();
            $output = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // If there is an error log it
            if ($stmt->error && DEBUG)
                print($stmt->error);

            $stmt->close();
            return $output;
        }
        return false;
    }
}
