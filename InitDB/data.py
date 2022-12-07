from datetime import datetime
from hashlib import sha256

class DataAdder:
    def __init__(self, cursor, db):
        self.cursor = cursor
        self.db = db
        self.songs = []
        self.artists = []
        self.albums = []
        self.distributors = []
        self.users = []

    def add_songs(self, songs):
        self.cursor.executemany("INSERT INTO SONG (Title, Duration, MusicFile) VALUES (%s, %s, %s)", songs)
        self.songs = songs

    def add_artists(self, artists):
        values = []
        for a in artists:
            v = [a[0], a[1]]
            v.append(f"profiles/{a[2]}.png")
            v.append(f"banners/{a[2]}.png")
            values.append(v)
        self.cursor.executemany("INSERT INTO ARTIST (Name, About, ProfilePicture, BannerPicture) VALUES (%s, %s, %s, %s)", values)
        self.artists = artists

    def add_albums(self, albums):
        values = []
        for a in albums:
            values.append([a[0], a[1], a[2], f"covers/{a[3]}.png", a[4]])
        self.cursor.executemany("INSERT INTO ALBUM (Title, Genre, ReleaseDate, CoverArt, IsSingle) VALUES (%s, %s, %s, %s, %s)", values)
        self.albums = albums

    def add_writes(self, song_artist_pairs):
        values = []
        for pair in song_artist_pairs:
            song_id = self.song_id_by_name(pair[0])
            artist_id = self.artist_id_by_name(pair[1])
            values.append((song_id, artist_id))
        self.cursor.executemany("INSERT INTO WRITES (SongID, ArtistID) VALUES (%s, %s)", values)
    
    def add_has(self, album_artist_pairs):
        values = []
        for pair in album_artist_pairs:
            album_id = self.album_id_by_name(pair[0])
            artist_id = self.artist_id_by_name(pair[1])
            values.append((album_id, artist_id))
        self.cursor.executemany("INSERT INTO HAS (AlbumID, ArtistID) VALUES (%s, %s)", values)

    def add_album_contains(self, album_song_pairs):
        values = []
        for pair in album_song_pairs:
            album_id = self.album_id_by_name(pair[0])
            song_id = self.song_id_by_name(pair[1])
            values.append((album_id, song_id))
        self.cursor.executemany("INSERT INTO ALBUM_CONTAINS (AlbumID, SongID) VALUES (%s, %s)", values)

    def add_stems(self, stems):
        values = []
        for s in stems:
            for i in range(s[1]):
                values.append((self.song_id_by_name(s[0]), i, f"{s[2]}/{str(i)}.mp3"))
        self.cursor.executemany("INSERT INTO STEM (SongID, StemNo, MusicFile) VALUES (%s, %s, %s)", values)

    def add_distributors(self, distributors):
        for d in distributors:
            self.cursor.execute("INSERT INTO DISTRIBUTOR (DistributorName) VALUES (%s)", [d])
        self.distributors = distributors

    def add_represents(self, artist_distributor_pairs):
        values = []
        for pair in artist_distributor_pairs:
            artist_id = self.artist_id_by_name(pair[0])
            dist_id = self.distributor_id_by_name(pair[1])
            values.append((artist_id, dist_id))
        self.cursor.executemany("INSERT INTO REPRESENTS (ArtistID, DistributorID) VALUES (%s, %s)", values)

    def add_users(self, users):
        for u in users:
            if u[2]:    # If premium
                self.cursor.execute("INSERT INTO USER (Username, PasswordHash, IsPremium, SubRenewDate) VALUES (%s, %s, %s, %s)", u)
            else:
                self.cursor.execute("INSERT INTO USER (Username, PasswordHash, IsPremium) VALUES (%s, %s, %s)", u)
        self.users = users
    
    def add_admins(self, usernames):
        values = []
        for u in usernames:
            values.append([self.user_id_by_name(u)])
        self.cursor.executemany("INSERT INTO ADMIN (AdminID) VALUES (%s)", values)

    def add_ads(self, ads):
        self.cursor.executemany("INSERT INTO ADVERTISEMENT (Duration, Company, SoundFile) VALUES (%s, %s, %s)", ads)

    def commit(self):
        self.db.commit()

    def song_id_by_name(self, song_name):
        i = 1
        for song in self.songs:
            if song[0] == song_name: return i
            i += 1
        return -1
    
    def artist_id_by_name(self, artist_name):
        i = 1
        for artist in self.artists:
            if artist[0] == artist_name: return i
            i += 1
        return -1
    
    def album_id_by_name(self, album_name):
        i = 1
        for album in self.albums:
            if album[0] == album_name: return i
            i += 1
        return -1
    
    def distributor_id_by_name(self, distributor_name):
        return self.distributors.index(distributor_name) + 1
    
    def user_id_by_name(self, username):
        i = 1
        for u in self.users:
            if u[0] == username: return i
            i += 1
        return -1
    
    def create_admin_playlist(self):
        self.cursor.execute("INSERT INTO PLAYLIST (PlaylistName, CreatorID) VALUES (%s, %s)", ("Playlist", "2"))
        self.cursor.execute("INSERT INTO PLAYLIST_CONTAINS (PlaylistID, SongID) VALUES (%s, %s)", ("1", "3"))
        self.cursor.execute("INSERT INTO PLAYLIST_CONTAINS (PlaylistID, SongID) VALUES (%s, %s)", ("1", "5"))
        self.cursor.execute("INSERT INTO PLAYLIST_CONTAINS (PlaylistID, SongID) VALUES (%s, %s)", ("1", "8"))

def initialize_data(cursor, db):
    print("Populating tables with default data.")
    d = DataAdder(cursor, db)
    d.add_songs([
        ("Meltdown", "00:04:28", "songs/motionless_in_white/meltdown.mp3"),
        ("Scoring the End of the World", "00:03:48", "songs/motionless_in_white/scoring_the_end_of_the_world.mp3"),
        ("The New Eternity", "00:03:25", "songs/silent_planet/the_new_eternity.mp3"),
        ("Northern Fires (Guernica)", "00:03:54", "songs/silent_planet/northern_fires.mp3"),
        ("Afterdusk", "00:03:55", "songs/silent_planet/afterdusk.mp3"),
        ("Share the Body", "00:03:33", "songs/silent_planet/share_the_body.mp3"),
        ("Firstborn (Ya'aburnee)", "00:05:07", "songs/silent_planet/firstborn.mp3"),
        ("22 Faces", "00:03:51", "songs/periphery/22_faces.mp3"),
        ("Omega", "00:11:45", "songs/periphery/omega.mp3"),
        ("The Pot", "00:06:18", "songs/tool/the_pot.mp3"),
        ("Rosetta Stoned", "00:11:13", "songs/tool/rosetta_stoned.mp3"),
        ("Elysium", "00:04:49", "songs/invent_animate/elysium.mp3"),
        ("Blinded", "00:03:22", "songs/as_i_lay_dying/blinded.mp3"),
        ("Stabbing in the Dark", "00:04:40", "songs/ice_nine_kills/stabbing_in_the_dark.mp3"),
        ("A Grave Mistake", "00:03:04", "songs/ice_nine_kills/a_grave_mistake.mp3"),
    ])
    d.add_artists([
        # Try not to make the about long because then the database is impossible to manually parse
        ("Motionless in White", 'Motionless in White is an American heavy metal band from Scranton, Pennsylvania. Formed in 2004, the band consists of lead vocalist Chris "Motionless" Cerulli, guitarists Ryan Sitkowski and Ricky "Horror" Olson, drummer Vinny Mauro and bassist Justin Morrow. The band has stated that their band name derived from the Eighteen Visions song "Motionless and White".', "motionless_in_white"),
        ("Silent Planet", "Silent Planet is an American metalcore band formed in Azusa, California, in 2009. Their name is derived from C. S. Lewis' science fiction novel Out of the Silent Planet. The group consists of vocalist Garrett Russell, guitarist Mitchell Stark, bassist Thomas Freckleton and drummer Alex Camarena.", "silent_planet"),
        ("Periphery", "Periphery is an American progressive metal band formed in Washington, D.C. in 2005. Their musical style has been described as progressive metal, djent, and progressive metalcore. They are considered one of the pioneers of the djent movement within progressive metal. They have also received a Grammy nomination.", "periphery"),
        ("Tool", "Tool is an American rock band from Los Angeles. Formed in 1990, the group's line-up includes vocalist Maynard James Keenan, guitarist Adam Jones and drummer Danny Carey. Justin Chancellor has been the band's bassist since 1995, replacing their original bassist Paul D'Amour.", "tool"),
        ("Invent Animate", "Invent Animate is an American progressive metalcore band from Port Neches, Texas. The band formed in late 2011 and self-released their debut EP titled Waves on March 13, 2012.", "invent_animate"),
        ("As I Lay Dying", "As I Lay Dying is an American metalcore band from San Diego, California. Founded in 2000 by vocalist Tim Lambesis, the band's first full lineup was completed in 2001. The band has released seven albums, one split album, and two compilation albums.", "as_i_lay_dying"),
        ("Ice Nine Kills", "Ice Nine Kills is an American heavy metal band from Boston, Massachusetts, who are signed to Fearless Records. Best known for its horror-inspired lyrics, Ice Nine Kills formed in its earliest incarnation in 2000 by high school friends Spencer Charnas and Jeremy Schwartz.", "ice_nine_kills"),
    ])
    d.add_albums([
        ("Scoring the End of the World", "Metal", datetime(2022, 6, 10), "scoring_the_end_of_the_world", False),
        ("When the End Began", "Metal", datetime(2018, 11, 2), "when_the_end_began", False),
        ("Juggernaut: Alpha", "Djent", datetime(2015, 1, 27), "juggernaut_alpha", False),
        ("Juggernaut: Omega", "Djent", datetime(2015, 1, 27), "juggernaut_omega", False),
        ("10,000 Days", "Rock", datetime(2006, 4, 28), "10000_days", False),
        ("Elysium", "Metal", datetime(2022, 11, 8), "elysium", True),
        ("Shaped by Fire", "Metal", datetime(2019, 9, 20), "shaped_by_fire", False),
        ("The Silver Scream", "Metal", datetime(2018, 10, 5), "the_silver_scream", False),
    ])
    d.add_writes([
        ("Meltdown", "Motionless in White"),
        ("Scoring the End of the World", "Motionless in White"),
        ("The New Eternity", "Silent Planet"),
        ("Northern Fires (Guernica)", "Silent Planet"),
        ("Afterdusk", "Silent Planet"),
        ("Share the Body", "Silent Planet"),
        ("Firstborn (Ya'aburnee)", "Silent Planet"),
        ("22 Faces", "Periphery"),
        ("Omega", "Periphery"),
        ("The Pot", "Tool"),
        ("Rosetta Stoned", "Tool"),
        ("Elysium", "Invent Animate"),
        ("Blinded", "As I Lay Dying"),
        ("Stabbing in the Dark", "Ice Nine Kills"),
        ("A Grave Mistake", "Ice Nine Kills"),
    ])
    d.add_has([
        ("Scoring the End of the World", "Motionless in White"),
        ("When the End Began", "Silent Planet"),
        ("Juggernaut: Alpha", "Periphery"),
        ("Juggernaut: Omega", "Periphery"),
        ("10,000 Days", "Tool"),
        ("Elysium", "Invent Animate"),
        ("Shaped by Fire", "As I Lay Dying"),
        ("The Silver Scream", "Ice Nine Kills"),
    ])
    d.add_album_contains([
        ("Scoring the End of the World", "Meltdown"),
        ("Scoring the End of the World", "Scoring the End of the World"),
        ("When the End Began", "The New Eternity"),
        ("When the End Began", "Northern Fires (Guernica)"),
        ("When the End Began", "Afterdusk"),
        ("When the End Began", "Share the Body"),
        ("When the End Began", "Firstborn (Ya'aburnee)"),
        ("Juggernaut: Alpha", "22 Faces"),
        ("Juggernaut: Omega", "Omega"),
        ("10,000 Days", "The Pot"),
        ("10,000 Days", "Rosetta Stoned"),
        ("Elysium", "Elysium"),
        ("Shaped by Fire", "Blinded"),
        ("The Silver Scream", "Stabbing in the Dark"),
        ("The Silver Scream", "A Grave Mistake"),
    ])
    d.add_stems([
        ("Meltdown", 2, "stems/motionless_in_white/meltdown"),
        ("Scoring the End of the World", 4, "stems/motionless_in_white/scoring_the_end_of_the_world"),
        ("The New Eternity", 3, "stems/silent_planet/the_new_eternity"),
        ("Northern Fires (Guernica)", 3, "stems/silent_planet/northern_fires"),
        ("Afterdusk", 4, "stems/silent_planet/afterdusk"),
        ("Share the Body", 2, "stems/silent_planet/share_the_body"),
        ("Firstborn (Ya'aburnee)", 4, "stems/silent_planet/firstborn"),
        ("22 Faces", 3, "stems/periphery/22_faces"),
        ("Omega", 5, "stems/periphery/omega"),
        ("The Pot", 3, "stems/tool/the_pot"),
        ("Rosetta Stoned", 5, "stems/tool/rosetta_stoned"),
        ("Elysium", 3, "stems/invent_animate/elysium"),
        ("Blinded", 4, "stems/as_i_lay_dying/blinded"),
        ("Stabbing in the Dark", 2, "stems/ice_nine_kills/stabbing_in_the_dark"),
        ("A Grave Mistake", 3, "stems/ice_nine_kills/a_grave_mistake"),
    ])
    d.add_distributors([
        "BigDistribute",
        "Yeet",
        "DistributorA",
        "Diztributor",
    ])
    d.add_represents([
        ("Motionless in White", "BigDistribute"),
        ("Silent Planet", "BigDistribute"),
        ("Periphery", "BigDistribute"),
        ("Tool", "Yeet"),
        ("Invent Animate", "DistributorA"),
        ("As I Lay Dying", "DistributorA"),
        ("Ice Nine Kills", "Diztributor"),
    ])
    d.add_users([
        ("Free", sha256(b'test').hexdigest(), False),
        ("Admin", sha256(b'test').hexdigest(), True, datetime(2025, 1, 1)),
        ("Free2", "06c0bf82cee370b32ab6194299edc8231350d8effbe96cb7f904a47d3188baa1", True, datetime(2023, 1, 4)),
    ])
    d.add_admins([
        "Admin",
    ])
    d.add_ads([
        ("01:00:01", "Live Nation", "ads/live_nation_0.mp3"),
        ("00:04:21", "Spotify", "ads/spotify_0.mp3"),
        ("00:03:51", "Apple Music", "ads/apple_music_0.mp3"),
    ])
    d.create_admin_playlist();
    d.commit()
