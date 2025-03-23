import subprocess
import os

def align(fasta_file, output_alignment):

    # Run Clustal Omega
    result = subprocess.run([
        "/usr/bin/clustalo",  # or "clustalomega" depending on the installation
        "-i", fasta_file,  # Input file
        "-o", output_alignment,  # Output file
        "--force",  # Overwrite if the file exists
        "--outfmt=clu"
    ], stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)

    #print("Clustal Omega STDOUT:", result.stdout)
    #print("Clustal Omega STDERR:", result.stderr)

    if result.returncode != 0:
        print("Error running Clustal Omega:", result.stderr)
        return None  # Exit function if Clustal Omega fails

    # Check if the alignment file was created
    if not os.path.exists(output_alignment):
        print("Error: Alignment file was not created!")
        return None
    else:
        return True