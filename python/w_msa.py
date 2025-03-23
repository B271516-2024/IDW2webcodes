import sys
import matplotlib.pyplot as plt
from Bio import AlignIO
import os
from align import align

def analyze_conservation(fasta_file):
    upload_dir = "/localdisk/home/s2667265/public_html/uploads/"
    fasta_file = os.path.abspath(fasta_file)
    output_alignment = os.path.join(upload_dir, "aligned_sequences.aln")

    if align(fasta_file, output_alignment): 
        alignment = AlignIO.read(output_alignment, "clustal")
    # Initialize a list to store conservation scores
    conservation_scores = []

    # Loop through each column in the alignment
    for i in range(alignment.get_alignment_length()):
        column = alignment[:, i]  # Get the i-th column
        unique_residues = set(column)  # Find unique residues in this column
        conservation_score = len(unique_residues)  # Number of unique residues
        conservation_scores.append(conservation_score)

    # Plot conservation scores
    plt.plot(conservation_scores)
    plt.xlabel("Position in Sequence")
    plt.ylabel("Unique Residues (Conservation Level)")
    plt.title("Protein Sequence Conservation")
    
    # Save plot as an image
    plot_filename = os.path.join(upload_dir, "conservation_plot.png")
    plt.savefig(plot_filename)
    plt.close()

    return plot_filename.replace("/localdisk/home/s2667265/public_html", "..")

if __name__ == "__main__":
    fasta_file = sys.argv[1]  # Get the uploaded file path
    image_path = analyze_conservation(fasta_file)
    print(image_path)  # Send the image path back to PHP
