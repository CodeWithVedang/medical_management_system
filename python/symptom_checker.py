import sys
import json
import mysql.connector
from collections import defaultdict
import logging
from decimal import Decimal

# Log file for debugging
logging.basicConfig(filename='python_debug.log', level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

# Database connection configuration
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "medical_system"  # Correct database name
}

# Simple rule-based system to map symptoms to diseases
SYMPTOM_TO_DISEASE = {
    "headache": ["Headache", "Fever"],
    "fever": ["Fever"],
    "body pain": ["Body Pain"],
    "runny nose": ["Common Cold"],
    "sneezing": ["Common Cold"],
    "cough": ["Common Cold"],
    "sore throat": ["Strep Throat"],
    "nasal congestion": ["Common Cold"]
}

def predict_disease(symptoms):
    logging.debug(f"Predicting disease for symptoms: {symptoms}")
    symptoms = [symptom.strip().lower() for symptom in symptoms.split(",")]
    predicted_diseases = set()

    for symptom in symptoms:
        if symptom in SYMPTOM_TO_DISEASE:
            predicted_diseases.update(SYMPTOM_TO_DISEASE[symptom])
            logging.debug(f"Symptom '{symptom}' mapped to diseases: {SYMPTOM_TO_DISEASE[symptom]}")
        else:
            logging.warning(f"Symptom '{symptom}' not found in SYMPTOM_TO_DISEASE")

    result = list(predicted_diseases) if predicted_diseases else ["Unknown Disease"]
    logging.debug(f"Predicted diseases: {result}")
    return result

def get_medicines_for_diseases(diseases):
    logging.debug(f"Fetching medicines for diseases: {diseases}")
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        placeholders = ",".join(["%s"] * len(diseases))
        query = f"""
            SELECT DISTINCT m.id, m.name, m.price, m.stock
            FROM medicines m
            JOIN disease_medicine_mapping dmm ON m.id = dmm.medicine_id
            WHERE dmm.disease IN ({placeholders})
        """
        logging.debug(f"Executing query: {query} with params: {diseases}")
        cursor.execute(query, diseases)
        medicines = cursor.fetchall()

        # Convert Decimal objects to float for JSON serialization
        for medicine in medicines:
            if isinstance(medicine['price'], Decimal):
                medicine['price'] = float(medicine['price'])

        cursor.close()
        conn.close()
        logging.debug(f"Medicines fetched: {medicines}")
        return medicines
    except mysql.connector.Error as err:
        logging.error(f"Database Error: {err}")
        return []

def store_feedback(symptoms, medicine_ids, rating):
    logging.debug(f"Storing feedback - Symptoms: {symptoms}, Medicine IDs: {medicine_ids}, Rating: {rating}")
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        query = "INSERT INTO symptom_feedback (symptom, medicine_id, rating) VALUES (%s, %s, %s)"
        for medicine_id in medicine_ids:
            cursor.execute(query, (symptoms, medicine_id, rating))
        conn.commit()

        cursor.close()
        conn.close()
        logging.debug("Feedback stored successfully")
        return True
    except mysql.connector.Error as err:
        logging.error(f"Database Error: {err}")
        return False

def main():
    try:
        logging.debug(f"Script started with arguments: {sys.argv}")
        if len(sys.argv) < 2:
            logging.error("Invalid arguments")
            print(json.dumps({"error": "Invalid arguments"}), file=sys.stdout)
            sys.exit(1)

        mode = sys.argv[1]

        if mode == "predict":
            if len(sys.argv) != 3:
                logging.error("Missing symptoms")
                print(json.dumps({"error": "Missing symptoms"}), file=sys.stdout)
                sys.exit(1)

            symptoms = sys.argv[2]
            if not symptoms:
                logging.error("Symptoms cannot be empty")
                print(json.dumps({"error": "Symptoms cannot be empty"}), file=sys.stdout)
                sys.exit(1)

            predicted_diseases = predict_disease(symptoms)
            if not predicted_diseases:
                logging.error("No diseases predicted")
                print(json.dumps({"error": "No diseases predicted"}), file=sys.stdout)
                sys.exit(1)

            medicines = get_medicines_for_diseases(predicted_diseases)
            if not medicines:
                logging.error("No medicines found for the predicted diseases")
                print(json.dumps({"error": "No medicines found for the predicted diseases"}), file=sys.stdout)
                sys.exit(1)

            result = {
                "diseases": predicted_diseases,
                "medicines": medicines
            }
            logging.debug(f"Returning result: {result}")
            print(json.dumps(result), file=sys.stdout)

        elif mode == "feedback":
            if len(sys.argv) != 5:
                logging.error("Missing feedback arguments")
                print(json.dumps({"error": "Missing feedback arguments"}), file=sys.stdout)
                sys.exit(1)

            symptoms = sys.argv[2]
            medicine_ids = sys.argv[3].split(",")
            rating = int(sys.argv[4])

            if not (1 <= rating <= 5) or not medicine_ids:
                logging.error("Invalid feedback data")
                print(json.dumps({"error": "Invalid feedback data"}), file=sys.stdout)
                sys.exit(1)

            success = store_feedback(symptoms, medicine_ids, rating)
            if success:
                logging.debug("Feedback success")
                print(json.dumps({"status": "success"}), file=sys.stdout)
            else:
                logging.error("Failed to store feedback")
                print(json.dumps({"error": "Failed to store feedback"}), file=sys.stdout)

        else:
            logging.error("Invalid mode")
            print(json.dumps({"error": "Invalid mode"}), file=sys.stdout)
            sys.exit(1)
    except Exception as e:
        logging.error(f"Unhandled exception: {str(e)}")
        print(json.dumps({"error": f"Script error: {str(e)}"}), file=sys.stdout)
        sys.exit(1)

if __name__ == "__main__":
    main()