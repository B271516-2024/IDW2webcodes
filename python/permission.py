import subprocess
import sys

# Function to create a directory
def create_directory(directory_path):
    try:
        # Create the directory using mkdir Bash command
        subprocess.run(['mkdir', directory_path], check=True)
        subprocess.run(['chmod', "777", directory_path], check=True)
    except subprocess.CalledProcessError as e:
        print(f"Error creating directory: {e}")

if len(sys.argv) != 2:
    print(f"Usage: python3 create_directory.py {sys.argv}")
    sys.exit(1)

directory_path = sys.argv[1]

create_directory(directory_path)