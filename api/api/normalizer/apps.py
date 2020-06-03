# from django.apps import AppConfig
# from django.conf import settings
# import os, sys
# import pickle
from django.apps import AppConfig


# class PredictorConfig(AppConfig):
#     path = os.path.join(settings.MODELS, 'bayessian_normalizer.py')
#     with open(path, 'rb') as pickled:
#         data = pickle.load(pickled)
#     regressor = data['regressor']
#     vectorizer = data['vectorizer']


class NormalizerConfig(AppConfig):
    name = 'normalizer'
