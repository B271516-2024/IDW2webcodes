import sys
from align import align
import subprocess
import os
import uuid

def analyze_conservation(fasta_file, userid):
    # Read the uploaded FASTA file and perform your conservation analysis here
    #upload_dir = "/localdisk/home/s2667265/public_html/uploads/"
    unique = str(uuid.uuid4())[:8]
    user_dir = "/localdisk/home/s2667265/public_html/userdata/" + userid +"/"
    fasta_file = os.path.abspath(fasta_file)
    align_file = os.path.join(user_dir, f"aligned_sequences_{unique}.aln")
    output_file = os.path.join(user_dir, f"similarity_plot_{unique}")

    if align(fasta_file, align_file):
        # Construct the plotcon command
        #os.environ['TMPDIR'] = '/localdisk/home/s2667265/public_html/uploads/'
        command = [
            "plotcon", 
            "-sequences", align_file,   # Input sequence file
            "-winsize", "20",           # Window size for the similarity plot
            "-graph", "png",             # Graph type (PostScript format)
            "-goutfile", output_file
        ]

        try:
            subprocess.run(command, check=True)
        except subprocess.CalledProcessError as e:
            print(f"Error while running plotcon: {e.stderr.decode()}")
        
        os.remove(align_file)
        #print(image_path1)
        output_file = output_file.replace("/localdisk/home/s2667265/public_html", "https://bioinfmsc8.bio.ed.ac.uk/~s2667265")
        output_file = output_file + ".1.png"
        return output_file

if __name__ == "__main__":
    fasta_file = sys.argv[1]  # Get the uploaded file path
    userid= sys.argv[2]
    image_path = analyze_conservation(fasta_file,userid)
    print(image_path)  # Send the image path back to PHP
