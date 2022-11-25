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

def initialize_data(cursor, db):
    print("Populating tables with default data.")
    d = DataAdder(cursor, db)
    d.add_songs([
        ("Meltdown", "00:04:28", "songs/motionless_in_white/scoring_the_end_of_the_world/meltdown.mp3"),
        ("Scoring the End of the World", "00:03:48", "songs/motionless_in_white/scoring_the_end_of_the_world/scoring_the_end_of_the_world.mp3"),
        ("The New Eternity", "00:03:25", "songs/silent_planet/when_the_end_began/the_new_eternity.mp3"),
        ("Northern Fires (Guernica)", "00:03:54", "songs/silent_planet/when_the_end_began/northern_fires.mp3"),
        ("Share the Body", "00:03:33", "songs/silent_planet/when_the_end_began/sharing_the_body.mp3"),
        ("Firstborn (Ya'aburnee)", "00:05:07", "songs/silent_planet/when_the_end_began/firstborn.mp3"),
        ("22 Faces", "00:03:51", "songs/periphery/juggernaut_alpha/22_faces.mp3"),
        ("Omega", "00:11:45", "songs/periphery/juggernaut_omega/omega.mp3"),
        ("The Pot", "00:06:18", "songs/tool/10000_days/the_pot.mp3"),
        ("Rosetta Stoned", "00:11:13", "songs/tool/10000_days/rosetta_stoned.mp3"),
        ("Elysium", "00:04:49", "songs/invent_animate/elysium/elysium.mp3"),
    ])
    d.add_artists([
        # Try not to make the about long because then the database is impossible to manually parse
        ("Motionless in White", "American metalcore band from Pennsylvania", "motionless_in_white"),
        ("Silent Planet", "American metalcore band formed in California", "silent_planet"),
        ("Periphery", "American progressive metal band formed in Washington, D.C", "periphery"),
        ("Tool", "American rock band from Los Angeles", "tool"),
        ("Invent Animate", "American progressive metalcore band from Port Neches, Texas", "invent_animate"),
    ])
    d.add_albums([
        ("Scoring the End of the World", "Metal", datetime(2022, 6, 10), "scoring_the_end_of_the_world", False),
        ("When the End Began", "Metal", datetime(2018, 11, 2), "when_the_end_began", False),
        ("Juggernaut: Alpha", "Djent", datetime(2015, 1, 27), "juggernaut_alpha", False),
        ("Juggernaut: Omega", "Djent", datetime(2015, 1, 27), "juggernaut_omega", False),
        ("10,000 Days", "Rock", datetime(2006, 4, 28), "10000_days", False),
        ("Elysium", "Metal", datetime(2022, 11, 8), "elysium", True),
    ])
    d.add_writes([
        ("Meltdown", "Motionless in White"),
        ("Scoring the End of the World", "Motionless in White"),
        ("The New Eternity", "Silent Planet"),
        ("Northern Fires (Guernica)", "Silent Planet"),
        ("Share the Body", "Silent Planet"),
        ("Firstborn (Ya'aburnee)", "Silent Planet"),
        ("22 Faces", "Periphery"),
        ("Omega", "Periphery"),
        ("The Pot", "Tool"),
        ("Rosetta Stoned", "Tool"),
        ("Elysium", "Invent Animate"),
    ])
    d.add_has([
        ("Scoring the End of the World", "Motionless in White"),
        ("When the End Began", "Silent Planet"),
        ("Juggernaut: Alpha", "Periphery"),
        ("Juggernaut: Omega", "Periphery"),
        ("10,000 Days", "Tool"),
        ("Elysium", "Invent Animate"),
    ])
    d.add_album_contains([
        ("Scoring the End of the World", "Meltdown"),
        ("Scoring the End of the World", "Scoring the End of the World"),
        ("When the End Began", "The New Eternity"),
        ("When the End Began", "Northern Fires (Guernica)"),
        ("When the End Began", "Share the Body"),
        ("When the End Began", "Firstborn (Ya'aburnee)"),
        ("Juggernaut: Alpha", "22 Faces"),
        ("Juggernaut: Omega", "Omega"),
        ("10,000 Days", "The Pot"),
        ("10,000 Days", "Rosetta Stoned"),
        ("Elysium", "Elysium"),
    ])
    d.add_stems([
        ("Meltdown", 2, "stems/motionless_in_white/meltdown"),
        ("Scoring the End of the World", 4, "stems/motionless_in_white/scoring_the_end_of_the_world"),
        ("The New Eternity", 3, "stems/silent_planet/the_new_eternity"),
        ("Northern Fires (Guernica)", 3, "stems/silent_planet/northern_fires"),
        ("Share the Body", 2, "stems/silent_planet/share_the_body"),
        ("Firstborn (Ya'aburnee)", 4, "stems/silent_planet/firstborn"),
        ("22 Faces", 3, "stems/periphery/22_faces"),
        ("Omega", 5, "stems/periphery/omega"),
        ("The Pot", 3, "stems/tool/the_pot"),
        ("Rosetta Stoned", 5, "stems/tool/rosetta_stoned"),
        ("Elysium", 3, "stems/invent_animate/elysium"),
    ])
    d.add_distributors([
        "BigDistribute",
        "Yeet",
        "DistributorA",
    ])
    d.add_represents([
        ("Motionless in White", "BigDistribute"),
        ("Silent Planet", "BigDistribute"),
        ("Periphery", "BigDistribute"),
        ("Tool", "Yeet"),
        ("Invent Animate", "DistributorA"),
    ])
    d.add_users([
        ("Alex", sha256(b'1qaz@WSX').hexdigest(), False),
        ("AlexAdmin", sha256(b'1qaz@WSX').hexdigest(), True, datetime(2025, 1, 1)),
    ])
    d.add_admins([
        "AlexAdmin",
    ])
    d.add_ads([
        ("01:00:01", "Live Nation", "ads/live_nation_0.mp3")
    ])
    d.commit()
