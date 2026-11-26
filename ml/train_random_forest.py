"""Train on real, authorized, de-identified labelled observations; no bundled data."""
import argparse
import csv
import json
from collections import Counter
from pathlib import Path
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import classification_report, balanced_accuracy_score, confusion_matrix
from sklearn.model_selection import train_test_split

FEATURES = ["marketing", "branding", "finance", "legal", "production"]
LABELS = ["pemasaran", "merek", "keuangan", "legalitas", "produksi"]

def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("csv_file", type=Path)
    parser.add_argument("--output", type=Path, default=Path("storage/app/private/ekraf-rf-model.json"))
    parser.add_argument("--approve", action="store_true", help="Publish artifact only after human review of validation results.")
    args = parser.parse_args()
    with args.csv_file.open(encoding="utf-8-sig", newline="") as stream:
        rows = list(csv.DictReader(stream))
    x, y = [], []
    for row in rows:
        values = [int(row[key]) for key in FEATURES]
        label = row["label"].strip()
        if not all(1 <= v <= 5 for v in values) or label not in LABELS:
            raise ValueError("Invalid feature scale or target label")
        x.append(values)
        y.append(label)
    counts = Counter(y)
    if len(x) < 100 or set(counts) != set(LABELS) or min(counts.values()) < 10:
        raise ValueError("Require at least 100 genuine observations and 10 per class; this is a minimum quality gate, not proof of validity.")
    x_train, x_test, y_train, y_test = train_test_split(x, y, test_size=0.25, random_state=42, stratify=y)
    model = RandomForestClassifier(n_estimators=100, max_depth=8, min_samples_leaf=3, class_weight="balanced", random_state=42, n_jobs=-1)
    model.fit(x_train, y_train)
    pred = model.predict(x_test)
    metrics = {"balanced_accuracy": float(balanced_accuracy_score(y_test, pred)),
               "report": classification_report(y_test, pred, labels=LABELS, output_dict=True, zero_division=0),
               "confusion_matrix": confusion_matrix(y_test, pred, labels=LABELS).tolist(),
               "train_count": len(x_train), "test_count": len(x_test)}
    print(json.dumps(metrics, indent=2, ensure_ascii=False))
    if not args.approve:
        print("Review validation, sampling, leakage, class performance and label quality before using --approve.")
        return
    trees = []
    for estimator in model.estimators_:
        t = estimator.tree_
        trees.append({"left": t.children_left.tolist(), "right": t.children_right.tolist(),
                      "feature": t.feature.tolist(), "threshold": t.threshold.tolist(),
                      "value": t.value[:, 0, :].tolist()})
    artifact = {"version": 1, "features": FEATURES, "classes": model.classes_.tolist(), "trees": trees, "metrics": metrics}
    args.output.parent.mkdir(parents=True, exist_ok=True)
    temporary = args.output.with_suffix(".tmp")
    temporary.write_text(json.dumps(artifact, separators=(",", ":")), encoding="utf-8")
    temporary.replace(args.output)
    print("Approved model artifact saved:", args.output)

if __name__ == "__main__":
    main()
