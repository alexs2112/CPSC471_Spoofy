# Unfinished
*Note: These are in no particular order, although some tasks will rely on the completion of other tasks*
 - User page
    - Welcome navigation page? Default to playlists page?
    - Only manage playlists, logout if you are logged in as the user (other users can see user pages)
 - Admin pages
    - List ads, add/approve ads
    - List songs, add songs and remove songs
        - Edit song page, to add `WRITES` and `ALBUM_CONTAINS` relations
        - A way to add stems to songs, probably also in the edit song page
    - List albums, add albums and remove albums
    - List artists, add artists and remove artists
    - The three music things should all be in the same page, under different tabs?
 - Playlists
    - Playlist page, remove song, play playlist
    - Songs will be added to playlists from the song details/song list/search pages
    - Shouldnt be able to add songs that are already present in the playlist, causes problems when deleting songs that have multiple entries
 - Search page
    - Add song to playlist
 - Song Page
    - Add song to playlist
    - Enable/Disable stems
 - Clean up all things tagged with `@todo`

## Stretch Goals
 - Actually being able to play music...

## Weird Bugs
*Unexpected functionalities that arent strictly incorrect*
 - Clicking `Next`/`Previous` on the song queue means that each time the page is reloaded the `NextSong` command is called again. (Resubmitting the button action)

# Finished
 - User login and registration
 - Admin:
    - Approve and revoke admin
    - Admin menubar for special actions
    - Delete users
    - List and view users
 - User Profile Page:
    - Display user details
    - Upgrade to Premium, Downgrade to Free
    - Logout
    - See user playlists, create and delete playlists from here
 - Album Page:
    - Album details
    - View artist
    - View song
    - Play Album
 - Artist Page
    - Artist details
    - View albums
    - View songs
 - Song Page
    - Song details
    - View associated albums
    - View associated artists
    - Add Song to Queue/Play Song
 - Music Queue
    - List of SongIDs and the SongIndex stored in the session
    - Inspect current song, next song, prev song, clear queue, shuffle queue from menubar
    - Menubar only appears if the queue is not null
    - Page to display the queue, remove songs, play songs, shuffle and clear queue
 - Search Page
    - Need to perform a mysql select query on Song, Album, Artist
    - View songs, albums, artists
 - Playlist Page
    - View songs
