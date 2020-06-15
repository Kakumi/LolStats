<?php
    ini_set("allow_url_fopen", 1);

    $KEY_API = "CLEF API"; //Disponible ici : https://developer.riotgames.com/
    if (isset($_GET['name'])) {
        $NAME = $_GET['name'];
    } else {
        $NAME = "Gamerbful";
    }
    $NAME_FORMAT = str_replace(" ", "_", $NAME);

    $jsonVersionDragon = file_get_contents("https://ddragon.leagueoflegends.com/api/versions.json");
    $versionsLol = json_decode($jsonVersionDragon, true)[0];

    $jsonChampions = file_get_contents("http://ddragon.leagueoflegends.com/cdn/" . $versionsLol . "/data/fr_FR/champion.json");
    $champions = json_decode($jsonChampions, true)['data'];

    $jsonItems = file_get_contents("http://ddragon.leagueoflegends.com/cdn/" . $versionsLol . "/data/fr_FR/item.json");
    $items = json_decode($jsonItems, true)['data'];

    $championsId = array();
    foreach ($champions as $champion) {
        $championsId[$champion['key']] = $champion['id'];
    }

    $jsonJoueur = file_get_contents("https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/" . $NAME_FORMAT . "?api_key=" . $KEY_API);
    $joueur = json_decode($jsonJoueur, true);

    $accountId = $joueur['accountId'];
    $puuid = $joueur['puuid'];
    $id = $joueur['id'];
    $level = $joueur['summonerLevel'];
    $icon = $joueur['profileIconId'];

    $jsonMasteries = file_get_contents("https://euw1.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-summoner/" . $id . "?api_key=" . $KEY_API);
    $masteries = json_decode($jsonMasteries, true);

    $jsonRanks = file_get_contents("https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/" . $id . "?api_key=" . $KEY_API);
    $ranks = json_decode($jsonRanks, true);

    $queueType = [
        "UNKNOWN" => "Inconnu",
        "CUSTOM" => "Personnalisé",
        "NORMAL_5x5_BLIND" => "Normal 5v5",
        "BOT_5x5" => "Bot 5v5",
        "BOT_5x5_INTRO" => "Bot 5v5 (Intro)",
        "BOT_5x5_BEGINNER" => "Bot 5v5 (Débutant)",
        "BOT_5x5_INTERMEDIATE" => "Bot 5v5 (Intermédiaire)",
        "NORMAL_3x3" => "Normal 3v3",
        "NORMAL_5x5_DRAFT" => "Normal 5v5 (Draft)",
        "ODIN_5x5_BLIND" => "Dominion 5v5",
        "ODIN_5x5_DRAFT" => "Dominion 5v5 (Draft)",
        "BOT_ODIN_5x5" => "Bot dominion 5v5",
        "RANKED_SOLO_5x5" => "Normal 5v5 (Classé)",
        "RANKED_PREMADE_3x3" => "Premade 3v3 (Classé)",
        "RANKED_PREMADE_5x5" => "Premade 5v5 (Classé)",
        "RANKED_TEAM_3x3" => "Team 3v3 (Classé)",
        "RANKED_TEAM_5x5" => "Team 5v5 (Classé)",
        "RANKED_FLEX_SR" => "Flex 5v5 (Classé)",
        "BOT_TT_3x3" => "Twisted Treeline Coop vs AI",
        "GROUP_FINDER_5x5" => "Bâtisseur d'équipe",
        "ARAM_5x5" => "Aram",
        "ONEFORALL_5x5" => "Un pour tous 5v5",
        "ONEFORALL_MIRRORMODE_5x5" => "Un pour tous 5v5 (Miroir)",
        "FIRSTBLOOD_1x1" => "Premier sang 1x1",
        "FIRSTBLOOD_2x2" => "Premier sang 2x2",
        "SR_6x6" => "Hexakill",
        "URF_5x5" => "Ultra Rapid Fire",
        "BOT_URF_5x5" => "Bot Ultra Rapid Fire",
        "NIGHTMARE_BOT_5x5_RANK1" => "Bot de l'enfer rang 1",
        "NIGHTMARE_BOT_5x5_RANK2" => "Bot de l'enfer rang 2",
        "NIGHTMARE_BOT_5x5_RANK5" => "Bot de l'enfer rang 3",
        "ASCENSION_5x5" => "Ascension",
        "HEXAKILL" => "Twisted Treeline Hexakill",
        "BILGEWATER_ARAM_5x5" => "Aram Bilgewater",
        "KING_PORO_5x5" => "Roi poro",
        "COUNTER_PICK" => "Nemesis Draft",
        "BILGEWATER_5x5" => "Bigelwater 5x5",
    ];

    $divisions = [
        "IRON" => "Fer",
        "BRONZE" => "Bronze",
        "SILVER" => "Argent",
        "GOLD" => "Or",
        "PLATINUM" => "Platine",
        "DIAMOND" => "Diamant",
        "MASTER" => "Maître",
        "GRAND_MASTER" => "Grand maître",
        "CHALLENGER" => "Challenger"
    ];

    $jsonPartie = file_get_contents("https://euw1.api.riotgames.com/lol/match/v4/matchlists/by-account/" . $accountId . "?api_key=" . $KEY_API);
    $parties = json_decode($jsonPartie, true)['matches'];

    $roles = [
        "TOP" => "Top",
        "MIDDLE" => "Mid",
        "JUNGLE" => "Jungle",
        "BOTTOM" => "ADC",
        "SUPPORT" => "Support",
        "NONE" => "Aucun"
    ];

    $gamemodes = [
        "CLASSIC" => "Classique",
        "ARAM" => "Aram",
        "URF" => "Ultra Rapid Fire"
    ];

    $statKda = [
        "WORTH" => [
            "kill" => 0,
            "death" => 0,
            "assist" => 0,
            "kda" => 100
        ],
        "BEST" => [
            "kill" => 0,
            "death" => 0,
            "assist" => 0,
            "kda" => 0
        ]
    ];
?>
<!DOCTYPE html>
<html lang="fr"></html>
<head>
    <title><?php echo $NAME; ?></title>
    <meta name="description" content="DESCRIPTION"/>
    <link rel="icon" type="image/png" href="https://raw.communitydragon.org/pbe/game/assets/ux/summonericons/profileicon<?php echo $icon; ?>.png">

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="keywords" content="League of Legends, Ilias, Compte, Statistiques">
    <meta name="author" content="Damien Brebion">

    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans|Lobster|McLaren|Roboto+Slab&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="style.css" type="text/css">

    <!-- Facebook -->
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image" content="LINK">
   	<meta property="og:title" content="PSEUDO"/>
   	<meta property="og:description" content="DESCRIPTION"/>
   	<meta property="og:url" content="SITE"/>
	<meta property="og:type" content="website"/>

    <!-- Script -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="public/script/script.js"></script>

</head>

<body data-spy="scroll" data-target="#navigation" data-offset="100">
    <section id="player">
        <div class="container">
            <div class="row pt-5 pb-5">
                <div class="col-md-3">
                    <img id="profil-icon" class="rounded-circle" src="https://raw.communitydragon.org/pbe/game/assets/ux/summonericons/profileicon<?php echo $icon; ?>.png" alt="icon">
                </div>
                <div class="col-md-9">
                    <h1><?php echo $NAME; ?></h1>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Niveau <?php echo $level; ?></li>
                                <li class="list-group-item">Champion<?php echo (sizeof($masteries) > 1 ? "s" : ""); ?> : <?php echo sizeof($masteries); ?></li>
                                <li id="top-kda" class="list-group-item">Meilleur KDA : Chargement</li>
                                <li id="worth-kda" class="list-group-item">Pire KDA : Chargement</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <?php
                                    foreach ($ranks as $rank) {
                                        $totalGames = $rank['losses'] + $rank['wins'];
                                        ?>

                                        <li class="list-group-item"><?php echo $queueType[$rank['queueType']]; ?> : <?php echo $divisions[$rank['tier']]; ?> <?php echo $rank['rank']; ?> (<span class="text-success"><?php echo $rank['wins']; ?></span>/<span class="text-danger"><?php echo $rank['losses']; ?></span>) -
                                        <?php
                                            if ($totalGames > 0) {
                                                echo number_format($rank['wins'] / $totalGames, 2, ',', '');
                                            } else {
                                                echo "100";
                                            }
                                            ?>
                                        %</li>
                                        <?php
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="champions">
        <div class="container">
            <h1>Champions</h1>
            <p><?php echo $NAME; ?> a actuellement jouer avec un total de <?php echo sizeof($masteries); ?> champion<?php echo (sizeof($masteries) > 1 ? "s" : ""); ?>.</p>
            <p>Voici ces 3 meilleurs</p>

            <div class="row">
                <?php
                    for ($i = 0; $i < 3; $i++) {
                        echo '<div class="col-md-4">';

                        if (isset($masteries[$i])) {
                            $championId = $masteries[$i]['championId'];
                            $championLevel = $masteries[$i]['championLevel'];
                            $championPoint = $masteries[$i]['championPoints'];

                            ?>

                            <h2 class="nom-champion"><?php echo $champions[$championsId[$championId]]['name']; ?></h2>
                            <p class="point-champion"><?php echo '<i class="fas fa-trophy" style="color: #f5d142;"></i> ' . number_format($championPoint, 0, ',', '.'); ?></p>

                            <?php
                                if ($championLevel >= 4) {
                                ?>
                                    <img class="image-mastery" src="http://raw.communitydragon.org/pbe/game/assets/loadouts/summoneremotes/rewards/mastery/em_champ_mastery_0<?php echo $championLevel; ?>_selector.png" alt="Photo masteries <?php echo $championLevel; ?>">
                                <?php
                            }
                            ?>

                            <img class="image-champion" src="http://raw.communitydragon.org/pbe/plugins/rcp-be-lol-game-data/global/default/v1/champion-splashes/<?php echo $championId; ?>/<?php echo $championId; ?>000.jpg" alt="image <?php echo $champions[$championsId[$championId]]['name']; ?>">

                            <?php
                        } else {
                            echo '<p>Aucun champion !</p>';
                        }
                        
                        echo '</div>';
                    }
                ?>
            </div>
        </div>
    </section>

    <section id="games">
         <div class="container">
            <h1>Parties</h1>
            <p>Voici la liste des dernières partie de <?php echo $NAME; ?></p>
            <div class="row">

            <?php
                for($i = 0; $i < 20 && isset($parties[$i]); $i++) {
                    $gameId = $parties[$i]['gameId'];

                    $partieJson = file_get_contents("https://euw1.api.riotgames.com/lol/match/v4/matches/" . $gameId . "?api_key=" . $KEY_API);
                    $partie = json_decode($partieJson, true);

                    foreach($partie['participantIdentities'] as $player) {
                        if ($player['player']['summonerId'] == $id) {
                            $participantId = $player['participantId'];
                        }
                    }

                   foreach ($partie['participants'] as $participant) {
                        if ($participant['participantId'] == $participantId) {
                            $stats = $participant['stats'];

                            $championId = $participant['championId'];
                            $role = $participant['timeline']['lane'];

                            $kill = $stats['kills'];
                            $assist = $stats['assists'];
                            $death = $stats['deaths'];

                            if ($death == 0) {
                            	$kda = ($kill + $assist);
                            } else {
                            	$kda = ($kill + $assist) / $death;
                            }
                            
                            if ($kda < $statKda["WORTH"]["kda"] && $kda > 0) {
                                $statKda["WORTH"]["kda"] = $kda;
                                $statKda["WORTH"]["kill"] = $kill;
                                $statKda["WORTH"]["death"] = $death;
                                $statKda["WORTH"]["assist"] = $assist;
                            } else {
                                if ($kda > $statKda["BEST"]["kda"]) {
                                    $statKda["BEST"]["kda"] = $kda;
                                    $statKda["BEST"]["kill"] = $kill;
                                    $statKda["BEST"]["death"] = $death;
                                    $statKda["BEST"]["assist"] = $assist;
                                }
                            }
                        }
                    }

                    if ($i == 0) {
                        if ($kda < 1) {
                            $color = 'fc0303';
                        } else if ($kda < 2) {
                            $color = 'fc4e03';
                        } else if ($kda < 3) {
                            $color = 'fc9003';
                        } else if ($kda < 4) {
                            $color = 'fcdf03';
                        } else if ($kda < 5) {
                            $color = '98fc03';
                        } else {
                            $color = '3dfc03';
                        }
                        ?>

                        <script>
                            let icon = document.getElementById("profil-icon");
                            icon.style.border = "8px solid #<?php echo $color; ?>";
                        </script>

                        <?php
                    }
                    ?>
                    <div class="col-md-2">
                        <img src="http://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/champion-icons/<?php echo $championId; ?>.png" alt="Icon <?php echo champions[$championsId[$championId]]['name']; ?>">
                    </div>
                    <div class="col-md-10">
                    	<?php echo $gameId; ?><br>
                        Champion : <?php echo $champions[$championsId[$championId]]['name']; ?><br>
                        Mode : <?php echo $gamemodes[$partie['gameMode']]; ?><br>
                        Rôle : <?php echo $roles[$role]; ?><br>
                        Kda : <?php echo $kda; ?> (K/D/A - <?php echo $kill; ?>/<?php echo $death; ?>/<?php echo $assist; ?>)<br>
                        <br>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </section>

    <script>
        window.onload = function() {
            let topKda = document.getElementById('top-kda');
            let worthKda = document.getElementById('worth-kda');

            topKda.innerHTML = "Meilleur KDA : <?php echo number_format($statKda['BEST']['kda'], 2, ',', ''); ?> (K/D/A - <span class='text-success'><?php echo $statKda['BEST']['kill']; ?></span>/<span class='text-danger'><?php echo $statKda['BEST']['death']; ?></span>/<span class='text-primary'><?php echo $statKda['BEST']['assist']; ?></span>)";
            worthKda.innerHTML = "Pire KDA : <?php echo number_format($statKda['WORTH']['kda'], 2, ',', ''); ?> (K/D/A - <span class='text-success'><?php echo $statKda['WORTH']['kill']; ?></span>/<span class='text-danger'><?php echo $statKda['WORTH']['death']; ?></span>/<span class='text-primary'><?php echo $statKda['WORTH']['assist']; ?></span>)";
        }
    </script>
</body>

</html>