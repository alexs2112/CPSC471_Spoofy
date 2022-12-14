# Unfinished
*Note: These are in no particular order, although some tasks will rely on the completion of other tasks*
 - Final Changes

## Stretch Goals
 - Fix songs having multiple albums/artists
 - We have a bunch of places that user redirections to other php files to call functionalities (the old admin functions mostly). These should be cleaned up to use buttons that call POST. Instead of redirecting to a page and relying on GET.
    - We can simple refactor the php functions into actual functions, then button presses will include the file and call the function

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
    - Add/Delete songs, albums, artists
    - Add/Remove artist credit to songs, albums
    - Add/Remove songs to albums
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
    - Enable/Disable stems by session
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
    - If the user is a free user they can only access and play advertisements
 - Playlist Page
    - View songs, remove songs
    - Play playlist
    - Delete playlist
    - Play songs
 - Advertisements
    - Free users can only access advertisements.
    - Anywhere they could see songs, ensure they have permissions, otherwise they only get the list of ads
    - Ad page, add ad to queue, play ad
 - Incrementing Song Plays
    - Playing a song directly will increment the number of TotalPlays and MonthlyPlays in both Song and Artist
    - Next, Previous, Shuffle all increment the number of plays
