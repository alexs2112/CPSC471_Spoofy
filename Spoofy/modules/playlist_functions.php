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
?>
