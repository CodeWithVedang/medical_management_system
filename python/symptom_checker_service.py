from flask import Flask, request, jsonify
import mysql.connector
from collections import defaultdict
import logging

app = Flask(__name__)

# Log file for debugging
logging.basicConfig(filename='python_debug.log', level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

# Database connection configuration
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "medical_system"
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

        cursor.close()
        conn.close()
        logging.debug(f"Medicines fetched: {medicines}")
        return medicines
    except mysql.connector.Error as err:
        logging.error(f"Database Error: {err}")
        return []

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        symptoms = data.get('symptoms', '')
        if not symptoms:
            return jsonify({"error": "Symptoms cannot be empty"}), 400

        predicted_diseases = predict_disease(symptoms)
        if not predicted_diseases:
            return jsonify({"error": "No diseases predicted"}), 400

        medicines = get_medicines_for_diseases(predicted_diseases)
        if not medicines:
            return jsonify({"error": "No medicines found for the predicted diseases"}), 400

        return jsonify({
            "diseases": predicted_diseases,
            "medicines": medicines
        })
    except Exception as e:
        logging.error(f"Error in /predict: {str(e)}")
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)