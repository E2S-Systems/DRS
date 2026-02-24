from fastapi import FastAPI

app = FastAPI(title="DRS BI Service", version="1.0.0")

@app.get("/")
def read_root():
    return {"status": "ok", "service": "Business Intelligence (DRS)", "message": "Python Engine is running!"}