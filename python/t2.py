import sys
import os

def process_data():
    # Suppress unwanted print statements
    sys.stdout = open(os.devnull, 'w')  # Redirect to "null", suppressing print statements

    # These print statements will not be visible
    print("This is an unwanted print statement.")
    
    # Reset stdout to console (show print statements again)
    sys.stdout = sys.__stdout__

    # This print statement will be shown on the console
    print("This is the final result.")

# Run the function
process_data()
