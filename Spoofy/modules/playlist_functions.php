<?php
function create_playlist($con, $playlist_name, $userID) {
    $prepare = mysqli_prepare($con, "INSERT INTO PLAYLIST (PlaylistName, CreatorID) VALUES (?, ?)");
    $prepare -> bind_param("ss", $playlist_name, $userID);
    $prepare -> execute();
}

function delete_playlist($con, $playlistID) {
    $prepare = mysqli_prepare($con, "DELETE FROM PLAYLIST WHERE PlaylistID=?");
    $prepare -> bind_param("s", $playlistID);
    $prepare -> execute();
}

function add_song($con, $playlistID, $songID) {
    $prepare = mysqli_prepare($con, "INSERT INTO PLAYLIST_CONTAINS (PlaylistID, SongID) VALUES (?, ?)");
    $prepare -> bind_param("ss", $playlistID, $songID);
    $prepare -> execute();
}

function remove_song($con, $playlistID, $songID) {
    // Prevent a user from adding a song to a playlist that already contains it
    $prepare = mysqli_prepare($con, "SELECT * FROM PLAYLIST_CONTAINS WHERE PlaylistID=? AND SongID=?");
    $prepare -> bind_param("ss", $playlistID, $songID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    if (mysqli_num_rows($result) > 0) { return; }

    $prepare = mysqli_prepare($con, "DELETE FROM PLAYLIST_CONTAINS WHERE PlaylistID=? AND SongID=?");
    $prepare -> bind_param("ss", $playlistID, $songID);
    $prepare -> execute();
}

function play_playlist($con, $playlistID) {
    if(!isset($_SESSION)) { session_start(); }
    $prepare = mysqli_prepare($con, "SELECT SongID FROM PLAYLIST_CONTAINS WHERE PlaylistID=?");
    $prepare -> bind_param("s", $playlistID);
    $prepare -> execute();
    $result = $prepare -> get_result();

    if (mysqli_num_rows($result) == 0) { return; }
    $_SESSION["Queue"] = array();
    $_SESSION["SongIndex"] = 0;
    while($row = mysqli_fetch_array($result)) {
        array_push($_SESSION["Queue"], $row["SongID"]);
    }
}
?>
