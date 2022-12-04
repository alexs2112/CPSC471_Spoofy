def drop(cursor, table):
    cursor.execute(f"DROP TABLE IF EXISTS {table}")

def drop_tables(cursor):
    print("Dropping tables in SpoofyDB.")
    drop(cursor, "STEM")
    drop(cursor, "WRITES")
    drop(cursor, "REPRESENTS")
    drop(cursor, "DISTRIBUTOR")
    drop(cursor, "HAS")
    drop(cursor, "ALBUM_CONTAINS")
    drop(cursor, "PLAYLIST_CONTAINS")
    drop(cursor, "PLAYLIST")
    drop(cursor, "ADMIN")
    drop(cursor, "ADVERTISEMENT")
    drop(cursor, "USER")
    drop(cursor, "ALBUM")
    drop(cursor, "ARTIST")
    drop(cursor, "SONG")

def create_tables(cursor):
    print("Creating tables in SpoofyDB.")
    cursor.execute("""CREATE TABLE SONG 
        (SongID INT NOT NULL AUTO_INCREMENT,
        TotalPlays INT DEFAULT 0,
        MonthlyPlays INT DEFAULT 0,
        Title VARCHAR(32),
        Duration VARCHAR(32),
        MusicFile VARCHAR(255),
        PRIMARY KEY (SongID)) """)
    cursor.execute("""CREATE TABLE ARTIST
        (ArtistID INT NOT NULL AUTO_INCREMENT,
        Name VARCHAR(32),
        About VARCHAR(1500),
        ProfilePicture VARCHAR(255),
        BannerPicture VARCHAR(255),
        TotalPlays INT DEFAULT 0,
        MonthlyPlays INT DEFAULT 0,
        PRIMARY KEY (ArtistID)) """)
    cursor.execute("""CREATE TABLE ALBUM
        (AlbumID INT NOT NULL AUTO_INCREMENT,
        IsSingle BOOLEAN NOT NULL,
        CoverArt VARCHAR(255),
        ReleaseDate DATE,
        Genre VARCHAR(255),
        Title VARCHAR(32),
        PRIMARY KEY (AlbumID)) """)
    cursor.execute("""CREATE TABLE USER
        (UserID INT NOT NULL AUTO_INCREMENT,
        Username VARCHAR(32) NOT NULL,
        PasswordHash CHAR(255) NOT NULL,
        IsPremium BOOLEAN NOT NULL,
        SubRenewDate DATE,
        PRIMARY KEY (UserID),
        UNIQUE (Username)) """)
    cursor.execute("""CREATE TABLE ADVERTISEMENT
        (AdID INT NOT NULL AUTO_INCREMENT,
        Duration VARCHAR(32),
        Company VARCHAR(32),
        SoundFile VARCHAR(255),
        PRIMARY KEY (AdID)) """)
    cursor.execute("""CREATE TABLE ADMIN
        (AdminID INT NOT NULL,
        PRIMARY KEY (AdminID),
        FOREIGN KEY (AdminID) REFERENCES USER(UserID) ON DELETE CASCADE ON
        UPDATE CASCADE) """)
    cursor.execute("""CREATE TABLE PLAYLIST
        (PlaylistID INT NOT NULL AUTO_INCREMENT,
        PlaylistName VARCHAR(32),
        CreatorID INT,
        PRIMARY KEY (PlaylistID),
        FOREIGN KEY (CreatorID) REFERENCES USER(UserID) ON DELETE SET NULL
        ON UPDATE CASCADE) """)
    cursor.execute("""CREATE TABLE PLAYLIST_CONTAINS
        (PlaylistID INT NOT NULL,
        SongID INT NOT NULL,
        PRIMARY KEY (PlaylistID, SongID),
        FOREIGN KEY (PlaylistID) REFERENCES PLAYLIST(PlaylistID) ON DELETE
        CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (SongID) REFERENCES SONG(SongID)ON DELETE CASCADE ON
        UPDATE CASCADE) """)
    cursor.execute("""CREATE TABLE ALBUM_CONTAINS
        (AlbumID INT NOT NULL,
        SongID INT NOT NULL,
        PRIMARY KEY (AlbumID, SongID),
        FOREIGN KEY (AlbumID) REFERENCES ALBUM(AlbumID) ON DELETE CASCADE
        ON UPDATE CASCADE,
        FOREIGN KEY (SongID) REFERENCES SONG(SongID) ON DELETE CASCADE ON
        UPDATE CASCADE) """)
    cursor.execute("""CREATE TABLE HAS
        (AlbumID INT NOT NULL,
        ArtistID INT NOT NULL,
        PRIMARY KEY (AlbumID, ArtistID),
        FOREIGN KEY (AlbumID) REFERENCES ALBUM(AlbumID) ON DELETE CASCADE
        ON UPDATE CASCADE,
        FOREIGN KEY (ArtistID) REFERENCES ARTIST(ArtistID) ON DELETE CASCADE ON
        UPDATE CASCADE) """)
    cursor.execute("""CREATE TABLE DISTRIBUTOR
        (DistributorID INT NOT NULL AUTO_INCREMENT,
        DistributorName VARCHAR(32),
        PRIMARY KEY (DistributorID)) """)
    cursor.execute("""CREATE TABLE REPRESENTS
        (ArtistID INT NOT NULL,
        DistributorID INT NOT NULL,
        PRIMARY KEY (ArtistID, DistributorID),
        FOREIGN KEY (ArtistID) REFERENCES ARTIST(ArtistID) ON DELETE CASCADE ON
        UPDATE CASCADE,
        FOREIGN KEY (DistributorID) REFERENCES DISTRIBUTOR(DistributorID)ON
        DELETE CASCADE ON UPDATE CASCADE) """)
    cursor.execute("""CREATE TABLE WRITES
        (SongID INT NOT NULL,
        ArtistID INT NOT NULL,
        PRIMARY KEY (SongID, ArtistID),
        FOREIGN KEY (SongID) REFERENCES SONG(SongID) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (ArtistID) REFERENCES ARTIST(ArtistID) ON DELETE CASCADE ON UPDATE CASCADE) """)
    cursor.execute("""CREATE TABLE STEM
        (SongID INT NOT NULL,
        StemNo INT NOT NULL,
        MusicFile VARCHAR(255),
        PRIMARY KEY (SongID, StemNo),
        FOREIGN KEY (SongID) REFERENCES SONG(SongID) ON DELETE CASCADE ON UPDATE CASCADE) """)
