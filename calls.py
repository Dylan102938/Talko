from flask import Flask, request, jsonify
from flask_cors import CORS
from google.cloud import vision
from google.cloud.vision import types
from google.cloud import translate
from yandex.Translater import Translater
import urllib
import urllib.request
import urllib.parse
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

@app.route('/translatesent', methods=["GET","POST"])
def translatesent():
    if request.method == "GET":
        text = request.args["q"]
        target = request.args["lang"]

        data = json.load(urllib.request.urlopen("http://api.urbandictionary.com/v0/define?term={" + urllib.parse.quote(text) + "}"))
        examples = []
        count = 0
        for example in data['list']:
            if count == 3:
                break

            if "*" not in example['example'] and "girl" not in example['example'] and len(example['example']) < 100:
                temp = example['example']
                temp = temp.replace('\r', '')
                temp = temp.replace('[', '')
                temp = temp.replace(']', '')
                temp = temp.replace('\"', '')
                temp = temp.rstrip('\n')
                examples.append(temp)
                count = count + 1

        translate_client = translate.Client()
        target = request.args["lang"]
        translated = []
        final = []
        length = len(examples)
        for x in range(0, length):
            translation = translate_client.translate(examples[x], target_language=target)
            translated.append(translation['translatedText'])

        for x in range (0,3):
            final.append({'ex': examples[x], 'tr' : translated[x]})

        return jsonify(final)

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