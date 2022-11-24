# Unfinished
*Note: These are in no particular order, although some tasks will rely on the completion of other tasks*
 - User login and registration
    - Dedicated login/register page
    - Don't allow users who are already logged in to access this page
 - User page
    - Welcome navigation page? Default to playlists page?
    - Upgrade to premium if free, cancel premium if premium
    - Logout
 - Admin pages
    - List users, promote user to admin
    - List ads, add/approve ads
    - List songs, add songs and remove songs
        - Edit song page, to add `WRITES` and `ALBUM_CONTAINS` relations
        - A way to add stems to songs, probably also in the edit song page
    - List albums, add albums and remove albums
    - List artists, add artists and remove artists
 - Playlists
    - View Songs
    - Playlist page, create new playlist for user, delete playlist
    - Playlist page, view song, remove song, play playlist, delete playlist
    - Songs will be added to playlists from the song details/song list/search pages
 - Music Queue
    - Can be stored as a list of song IDs and a current index in a session
    - Page for the queue, shuffle, remove from queue, next song, previous song, view song
 - Search page
    - Need to perform a mysql select query on Song, Album, Artist
    - View songs, albums, artists
    - Add song to playlist
 - Album page
    - Album details
    - View artist
    - View song
    - Play album
 - Artist Page
    - Artist details
    - Tab to view albums
    - Tab to view songs
 - Song Page
    - Song details
    - Add song to playlist
    - Add song to queue
        - Play song (set the queue as `[SongID]`)
    - Enable/Disable stems
    - View associated albums
    - View associated artists

# Stretch Goals
 - Actually being able to play music...

# Finished
 - User login and registration
