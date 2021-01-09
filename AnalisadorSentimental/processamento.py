from leia import SentimentIntensityAnalyzer 
from flask import Flask, jsonify, request

app = Flask(__name__)

s = SentimentIntensityAnalyzer()

@app.route('/api/sentimento', methods=['GET', 'POST'])
def api():
    message = request.json['message']
    analisador = s.polarity_scores(message)
    return jsonify({"data":analisador}), 200


app.run()
