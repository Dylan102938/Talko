from flask import Flask, request, jsonify
from flask_cors import CORS
from google.cloud import vision
from google.cloud.vision import types
from google.cloud import translate
import json
import os
import io

app = Flask(__name__)
CORS(app)

os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = "C:\wamp64\www\Talko\creds.json"

@app.route('/objrec',methods=["GET","POST"])
def objrec():
    if request.method == "GET":
        client = vision.ImageAnnotatorClient()

        file_name = os.path.join(
            os.path.dirname(__file__),
            request.args["image"])

        with io.open(file_name, 'rb') as image_file:
            content = image_file.read()

        image = types.Image(content=content)

        response = client.label_detection(image=image)
        labels = response.label_annotations
        objects = []

        for label in labels:
            objects.append(label.description)
        return json.dumps(objects)

@app.route('/translateobj', methods=["GET","POST"])
def translateobj():
    if request.method == "GET":
        translate_client = translate.Client()
        text = request.args["q"]
        target = request.args["lang"]
        translation = translate_client.translate(text, target_language=target)
        return translation['translatedText']

if __name__ == '__main__':
    app.run(host="0.0.0.0",port=5000,debug=True)