# Unfinished
*Note: These are in no particular order, although some tasks will rely on the completion of other tasks*
 - Admin pages
    - List ads, add/approve ads
    - List songs, add songs and remove songs
        - Edit song page, to add `WRITES` and `ALBUM_CONTAINS` relations
        - A way to add stems to songs, probably also in the edit song page
    - List albums, add albums and remove albums
    - List artists, add artists and remove artists
    - The three music things should all be in the same page, under different tabs?
 - Module for Queue functions similar to playlist_functions
    - Call to play/add to queue individual ads from Search Page, Advertisement Page
 - Song Page
    - Enable/Disable stems
 - Advertisements
    - Edit our previous reports and deliverables that mention ad functionality that is inconsistent with the work done
 - Clean up all things tagged with `@todo`
    - Make sure that all `href` blocks are using project root (their paths should always start with `/`)
    - Make sure all `$prepare`s are closed

## Stretch Goals
 - Actually being able to play music...
 - Fix songs having multiple albums/artists

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
    - See user playlists, create, delete, and play playlists from here
 - Album Page:
    - Album details
    - View artist
    - View song
    - Play Album
    - Play songs
 - Artist Page
    - Artist details
    - View albums
    - View songs
    - Play songs, albums
 - Song Page
    - Song details
    - View associated albums
    - View associated artists
    - Add song to Queue/Play Song
    - Add song to playlist
 - Music Queue
    - List of SongIDs and the SongIndex stored in the session
    - Inspect current song, next song, prev song, clear queue, shuffle queue from menubar
    - Menubar only appears if the queue is not null
    - Page to display the queue, remove songs, play songs, shuffle and clear queue
 - Search Page
    - Need to perform a mysql select query on Song, Album, Artist
    - View songs, albums, artists
    - Add song to playlist
    - Play songs, albums
 - Playlist Page
    - View songs, remove songs
    - Play playlist
    - Delete playlist
    - Play songs
 - Advertisements
    - Free users can only access advertisements.
    - Anywhere they could see songs, ensure they have permissions, otherwise they only get the list of ads
    - Ad page, add ad to queue, play ad
