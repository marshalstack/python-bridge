import importlib
import json
import sys
from dataclasses import dataclass

from quart import Quart, request
from quart_schema import QuartSchema, validate_request

app = Quart('marshal:python-bridge')
QuartSchema(app)

@dataclass
class RequestPayload:
    module: str
    function: str
    paths: str
    args: str

@app.post('/')
@validate_request(RequestPayload)
async def main(data: RequestPayload):
    data = await request.get_json()

    # add app search paths
    paths = json.loads(data['paths'])
    for p in paths:
        sys.path.insert(1, p)

    # import module
    module = importlib.import_module(data['module'])
    func = module.__getattribute__(data['function'])

    # execute function
    args = json.loads(data['args'])
    if not args:
        return func()

    return func(**args)

if __name__ == "__main__":
    app.run()
