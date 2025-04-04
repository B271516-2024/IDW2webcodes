import sys
import json
import urllib.parse
import urllib.request
import xml.etree.ElementTree as ET
import traceback

from Bio import Entrez, SeqIO

def search_ncbi_protein(protein_name, taxon_name, userid, email="s2667265@ed.ac.uk"):
    # Set email and apikey
    Entrez.email = email
    Entrez.api_key = "ad817beb29bc7caecc6adc4a802660ece809"

    # Search query with protein name and taxon filter
    if taxon_name.isdigit():
        query = f"{protein_name}[Protein Name] AND txid{taxon_name}[Organism]"
    else:
        query = f"{protein_name}[Protein Name] AND {taxon_name}[Organism]"
 
    # Step 1: Search for protein IDs
    try:
        # Step 1: Search for protein IDs
        search_handle = Entrez.esearch(db="protein", term=query)
        search_results = Entrez.read(search_handle)
        search_handle.close()

        #print(json.dumps({"Search results": search_results}))  # Debugging print

        protein_ids = search_results.get("IdList", [])

        if not protein_ids:
            print(json.dumps({"No matching proteins found for query": query}))
            return None

        # Step 2: Fetch sequences in FASTA format
        fetch_handle = Entrez.efetch(db="protein", id=",".join(protein_ids), rettype="fasta", retmode="text")
        fasta_sequences = fetch_handle.read()
        fetch_handle.close()
        #print(json.dumps({"Search results": fasta_sequences}))

        protein_name=protein_name.replace(" ", "_")
        protein_name=protein_name.replace("-","_")

        file_path = f"/home/s2667265/public_html/userdata/{userid}/{protein_name}_{taxon_name}.fasta"
        with open(file_path, 'w') as f:
            f.write(fasta_sequences)

        downloadpath= file_path.replace("/home/s2667265/public_html", "bioinfmsc8.bio.ed.ac.uk/~s2667265")
        print(file_path)
        #return {"file_path": file_path}
        pass

    except Exception as e:
        print(json.dumps({"Error during NCBI query": e}))
        print("Traceback:", traceback.format_exc())
        return None

# Main execution
if __name__ == "__main__":
    try:
        taxon_name =sys.argv[1]
        userid = sys.argv[2]
        protein_name = " ".join(sys.argv[3:])

        #if check_inputs(protein_name, taxon_name):
            # Proceed with the script if both are non-empty
            #print(json.dumps({"Processed with: protein_name=": protein_name, "taxon_name=": taxon_name}))
        search_ncbi_protein(protein_name, taxon_name, userid)
        #else:
        #   print(json.dumps({"Please check input."}))
    except Exception as e:
        print(json.dumps({"Error in execution": str(e)}))  # Output error message
        print("Traceback:", traceback.format_exc())  # Print traceback
        sys.exit(1)
