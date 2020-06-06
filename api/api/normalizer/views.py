import os
import sys

import json
from django.http import JsonResponse, HttpResponse
from django.shortcuts import render
from django.template import loader
from django.views import View
from rest_framework.decorators import api_view
from rest_framework.response import Response
from .bayessian_normalizer import normalize as nb_normalizer
# from . import templates
# class call_model(APIView):
#     def get(self, request):
#         if request.method == 'GET':
#             nb_normalizer = request.GET.get('naive-bayes-normalizer')
#
#             vector = PredictorConfig.vectorizer.transform([nb_normalizer])
#             prediction = PredictorConfig.regressor.predict(vector)[0]
#             response = {'accuracy': prediction}
#             return JsonResponse(response)


@api_view(['POST'])
def normalize(request, format=None):
    response = nb_normalizer(request.data['text'])
    if (response != None):
        return HttpResponse(json.dumps(response), content_type='application/json')
    else:
        return Response(status=500)

def index(request):
    return render(request, 'index.html')
