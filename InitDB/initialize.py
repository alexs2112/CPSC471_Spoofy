import mysql.connector, sys
from create_tables import drop_tables, create_tables
from data import initialize_data

def help():
    print("""
Usage: python initialize.py [option]

Options:
help:       Print this menu.
refresh:    Drop existing tables in SpoofyDB. Recreate them with default data.
check:      Check the mysql connection to SpoofyDB under spoofyUser.
drop:       Drop existing tables in SpoofyDB. Results in empty database.
init:       Creates tables populated with default data. Will break on non-empty db.""")

def establish_connection():
    try:
        db = mysql.connector.connect(
            host = "localhost",
            user = "spoofyUser",
            password = "testing",
            database = "SpoofyDB"
        )
        print("Connection to SpoofyDB established.")
    except:
        print("Could not create mysql connection.")
        print("Consult README.md for more details.")
        exit(1)
    return db

if __name__ == "__main__":
    if (len(sys.argv) < 2 or "help" == sys.argv[1]):
        help()
        exit(0)

    opt = sys.argv[1]
    if (opt in ["check", "drop", "init", "refresh"]):
        db = establish_connection()
        cursor = db.cursor()
    else:
        print(f"Invalid argument {opt}.")
        help()
        exit(1)

    if (opt in ["drop", "refresh"]):
        drop_tables(cursor)

    if (opt in ["init", "refresh"]):
        create_tables(cursor)
        initialize_data(cursor, db)
