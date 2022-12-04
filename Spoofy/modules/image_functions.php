<?php
function song_cover($con, $songID) {
    // clearstatcache();       //file_exists caches the result, uncomment this to clear the cache
    $sql = "SELECT CoverArt FROM ALBUM WHERE AlbumID IN (SELECT AlbumID FROM ALBUM_CONTAINS WHERE SongID=?)";
    $prepare = mysqli_prepare($con, $sql);
    $prepare -> bind_param("s", $songID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    $row = mysqli_fetch_array($result);
    if (mysqli_num_rows($result) == 0 || !file_exists('../resources/'.$row["CoverArt"])) {
        $path = "covers/unknown.png";
    } else {
        $path = $row["CoverArt"];
    }
    $prepare -> close();
    return $path;
}
function album_cover($con, $albumID) {
    $sql = "SELECT CoverArt FROM ALBUM WHERE AlbumID=?";
    $prepare = mysqli_prepare($con, $sql);
    $prepare -> bind_param("s", $albumID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    $row = mysqli_fetch_array($result);
    if (mysqli_num_rows($result) == 0 || !file_exists('../resources/'.$row["CoverArt"])) {
        $path = "covers/unknown.png";
    } else {
        $path = $row["CoverArt"];
    }
    $prepare -> close();
    return $path;
}
function artist_profile($con, $artistID) {
    $sql = "SELECT ProfilePicture FROM ARTIST WHERE ArtistID=?";
    $prepare = mysqli_prepare($con, $sql);
    $prepare -> bind_param("s", $artistID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    $row = mysqli_fetch_array($result);
    if (mysqli_num_rows($result) == 0 || !file_exists('../resources/'.$row["ProfilePicture"])) {
        $path = "profiles/unknown.png";
    } else {
        $path = $row["ProfilePicture"];
    }
    $prepare -> close();
    return $path;
}
function artist_banner($con, $artistID) {
    $sql = "SELECT BannerPicture FROM ARTIST WHERE ArtistID=?";
    $prepare = mysqli_prepare($con, $sql);
    $prepare -> bind_param("s", $artistID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    $row = mysqli_fetch_array($result);
    if (mysqli_num_rows($result) == 0 || !file_exists('../resources/'.$row["BannerPicture"])) {
        $path = "banners/unknown.png";
    } else {
        $path = $row["BannerPicture"];
    }
    $prepare -> close();
    return $path;
}
?>
