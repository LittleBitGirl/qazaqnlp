import os
from io import open
from collections import Counter
import array
import re, sys
import codecs, string

def words(text): return re.findall(r'\w+', text.lower())
THIS_FOLDER = os.path.dirname(os.path.abspath(__file__))
WORDS = Counter(
    words(
        codecs.open(THIS_FOLDER+'/library/all_words.txt', 'r', 'utf_8').read().translate(str.maketrans('', '', string.punctuation))
        + codecs.open(THIS_FOLDER+'/library/ocr.txt', 'r', 'utf_8').read().translate(str.maketrans('', '', string.punctuation))
        + codecs.open(THIS_FOLDER+'/library/abay_joli_1_full.txt', 'r', 'utf_8').read().translate(
            str.maketrans('', '', string.punctuation))
        + codecs.open(THIS_FOLDER+'/library/bir_ata_bala_full.txt', 'r', 'utf_8').read().translate(
            str.maketrans('', '', string.punctuation))
    )
)


def P(word, N=sum(WORDS.values())):
    "Probability of `word`."
    return WORDS[word] / N


def correction(word):
    "Most probable spelling correction for word."
    return max(candidates(word), key=P)


def candidates(word):
    "Generate possible spelling corrections for word."
    return (known([word]) or known(edits1(word)) or known(edits2(word)) or [word])


def known(words):
    "The subset of `words` that appear in the dictionary of WORDS."
    return set(w for w in words if w in WORDS)


def edits1(word):
    "All edits that are one edit away from `word`."
    letters = 'аәбвгғдеёжзийкқлмнңоөпрстуүұфхһцчшщыіэюя'
    splits = [(word[:i], word[i:]) for i in range(len(word) + 1)]
    deletes = [L + R[1:] for L, R in splits if R]
    transposes = [L + R[1] + R[0] + R[2:] for L, R in splits if len(R) > 1]
    replaces = [L + c + R[1:] for L, R in splits if R for c in letters]
    inserts = [L + c + R for L, R in splits for c in letters]
    return set(deletes + transposes + replaces + inserts)


def edits2(word):
    "All edits that are two edits away from `word`."
    return (e2 for e1 in edits1(word) for e2 in edits1(e1))


def tokenize(text):
    text = text.translate(str.maketrans('', '', string.punctuation))
    return re.split('\s+', text)


def LD(a, b, mx=-1):
    def result(d):
        return d if mx < 0 else False if d > mx else True

    if a == b: return result(0)
    la, lb = len(a), len(b)
    if mx >= 0 and abs(la - lb) > mx: return result(mx + 1)
    if la == 0: return result(lb)
    if lb == 0: return result(la)
    if lb > la: a, b, la, lb = b, a, lb, la
    cost = array('i', range(lb + 1))

    for i in range(1, la + 1):
        cost[0] = i
        ls = i - 1
        mn = ls
        for j in range(1, lb + 1):
            ls, act = cost[j], ls + int(a[i - 1] != b[j - 1])
            cost[j] = min(ls + 1, cost[j - 1] + 1, act)
            if (ls < mn): mn = ls
        if mx >= 0 and mn > mx: return result(mx + 1)
    if mx >= 0 and cost[lb] > mx: return result(mx + 1)
    return result(cost[lb])


def minimumEditDistance(s1, s2):
    if len(s1) > len(s2):
        s1, s2 = s2, s1
    distances = range(len(s1) + 1)
    for index2, char2 in enumerate(s2):
        newDistances = [index2 + 1]
        for index1, char1 in enumerate(s1):
            if char1 == char2:
                newDistances.append(distances[index1])
            else:
                newDistances.append(1 + min((distances[index1],
                                             distances[index1 + 1],
                                             newDistances[-1])))
        distances = newDistances
    return distances[-1]


def getAccuracy(word, corrected):
    levenshtein = minimumEditDistance(word, corrected)
    return (1 - levenshtein / len(corrected)) * 100


def normalize(text):
    wordsArray = tokenize(text)
    normalizedArray = []
    overall_accuracy = 0
    count = 0
    corrected_count = 0
    for word in wordsArray:
        corrected = correction(word)
        accuracy = getAccuracy(word, corrected)
        count += 1
        overall_accuracy += accuracy
        if (corrected != word):
            normalizedArray.append({
                "word": word,
                "corrected": corrected,
                "LD/length": accuracy
            })
            corrected_count += 1
        else:
            normalizedArray.append({
                "word": word
            })

    return {
        "normalized_array": normalizedArray,
        "average_accuracy": overall_accuracy / count,
        "num_of_word": count,
        "num_of_corrected": corrected_count
    }
    # print("___________________________________Normalized Array_______________________________________________")
    # print(normalizedArray)
    # print("______________________________________Average Accuracy__________________________________________________")
    # print(overall_accuracy / count)
    # print("______________________________________Number of words_____________________________________________")
    # print(count)
    # print("________________________________Number of corrected words_________________________________________")
    # print(corrected_count)


# def preProcess(data_list):
#
#
# def preProcessForTest(data_list):
#     le = preprocessing.LabelEncoder()
#     le.fit(data_list)
#     data_list = le.transform(data_list).reshape(-1, 1)
#     enc = preprocessing.OneHotEncoder(dtype=float, handle_unknown='ignore')
#     return enc.transform(data_list)

if __name__ == "__main__":
    text = open('library/text.txt', 'r', encoding='utf-8')
    normalize(text)
